<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\EduYear;
use App\Models\Language;
use App\Models\Level;
use App\Models\Specialty;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectList;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class HemisController extends Controller
{
    private function handleOAuthAuthorization(Request $request, GenericProvider $provider, string $redirectPath)
    {
        if (!$request->has('code')) {
            if ($request->has('start')) {
                $authorizationUrl = $provider->getAuthorizationUrl();
                Session::put('oauth2state', $provider->getState());
                return redirect($authorizationUrl);
            } else {
                return redirect($redirectPath . '?start=1');
            }
        } else if (empty($request->state) || (Session::has('oauth2state') && $request->state !== Session::get('oauth2state'))) {
            Session::forget('oauth2state');
            return response('Invalid state', 400);
        }

        return null;
    }


    /**
     * Handle user authentication via HEMIS OAuth
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function user(Request $request)
    {
        $employeeProvider = new GenericProvider([
            'clientId' => config('services.hemis.client_id'),
            'clientSecret' => config('services.hemis.client_secret'),
            'redirectUri' => config('services.hemis.redirect_user'),
            'urlAuthorize' => config('services.hemis.user_url') . '/oauth/authorize',
            'urlAccessToken' => config('services.hemis.user_url') . '/oauth/access-token',
            'urlResourceOwnerDetails' => config('services.hemis.user_url') . '/oauth/api/user?fields=id,uuid,employee_id_number,type,roles,name,login,email,picture,firstname,surname,patronymic,birth_date,university_id,phone'
        ]);

        // Handle OAuth authorization
        $authResponse = $this->handleOAuthAuthorization($request, $employeeProvider, '/login/user/');
        if ($authResponse) {
            return $authResponse;
        }

        try {
            $accessToken = $employeeProvider->getAccessToken('authorization_code', [
                'code' => $request->code
            ]);
            $resourceOwner = $employeeProvider->getResourceOwner($accessToken);
            $user_array = $resourceOwner->toArray();
            $roles = [];
            $super_roles = [
                2161,
                1349,
                1568
            ];
            foreach ($user_array['roles'] as $role) $roles[] = $role['code'];
            if (in_array($user_array['employee_id'], $super_roles)) $roles[] = 'super_admin';
            $user = User::updateOrCreate(
                ['id' => $user_array['employee_id']],
                [
                    'name' => json_encode([
                        'full_name' => $user_array['name'],
                        'first_name' => $user_array['firstname'],
                        'second_name' => $user_array['surname'],
                        'third_name' => $user_array['patronymic'],
                        'short_name' => $user_array['surname'] . ' ' . $user_array['firstname'][0] . '. ' . $user_array['patronymic'][0],
                    ]),
                    'hemis_id' => $user_array['employee_id_number'],
                    'hemis_roles' => json_encode($roles),
                    'uuid' => $user_array['uuid'],
                    'picture' => $user_array['picture'],
                ]
            );
            foreach ($user_array['departments'] as $department) {
                Workplace::updateOrCreate([
                    'user_id' => $user_array['employee_id'],
                    'department_id' => $department['department']['id'],
                    'head_type' => $department['staffPosition']['code'] == '16' ? 'department' : 'user',
                    'is_main' => $department['employmentForm']['code'] == '11' ? '1' : '0',
                ]);
            }
            $user->assignRole(end($roles));
            Auth::login($user);
            return redirect(route('home'));
        } catch (IdentityProviderException $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Handle student authentication via HEMIS OAuth
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function student(Request $request)
    {
        $employeeProvider = new GenericProvider([
            'clientId'                => config('services.hemis.client_id'),
            'clientSecret'            => config('services.hemis.client_secret'),
            'redirectUri'             => config('services.hemis.redirect_stud'), // ✅ to'g'irlandi
            'urlAuthorize'            => config('services.hemis.student_url') . '/oauth/authorize',
            'urlAccessToken'          => config('services.hemis.student_url') . '/oauth/access-token',
            'urlResourceOwnerDetails' => config('services.hemis.student_url') . '/oauth/api/user?fields=id,uuid,employee_id_number,type,roles,name,login,email,picture,firstname,surname,patronymic,birth_date,university_id,phone'
        ]);

        $authResponse = $this->handleOAuthAuthorization($request, $employeeProvider, '/login/student/');
        if ($authResponse) return $authResponse;

        try {
            $accessToken   = $employeeProvider->getAccessToken('authorization_code', [
                'code' => $request->code
            ]);
            $resourceOwner = $employeeProvider->getResourceOwner($accessToken);
            $student_array = $resourceOwner->toArray()['data'];

            $specialty = Specialty::firstOrCreate(
                [
                    'uuid'          => $student_array['specialty']['id'],
                    'department_id' => $student_array['faculty']['id'],
                ],
                [
                    'name' => $student_array['specialty']['name'],
                    'code' => $student_array['specialty']['code'],
                ]
            );

            $language = Language::firstOrCreate(
                ['id'   => $student_array['group']['educationLang']['code']],
                ['name' => $student_array['group']['educationLang']['name']]
            );

            $response    = Http::withToken(config('services.hemis.token'))
                ->timeout(15)
                ->get(config('services.hemis.student_url') . '/rest/v1/data/student-list', [
                    'search' => $student_array['student_id_number']
                ]);
            $student_api = $response->json()['data']['items'][0] ?? null;

            if (!$student_api) {
                \Log::error('HEMIS student-list bo\'sh qaytdi', ['student_id' => $student_array['student_id_number']]);
                return response('Talaba ma\'lumotlari topilmadi.', 500);
            }

            $student = Student::updateOrCreate(
                ['id' => $student_array['student_id_number']],
                [
                    'name' => json_encode([
                        'first_name'  => $student_array['first_name'],
                        'second_name' => $student_array['second_name'],
                        'third_name'  => $student_array['third_name'],
                        'full_name'   => $student_array['full_name'],
                        'short_name'  => $student_array['short_name'],
                    ]),
                    'hemis_id'      => $student_array['id'],
                    'picture'       => $student_array['image'],
                    'curriculum_id' => $student_api['_curriculum'],
                    'specialty_id'  => $specialty->id,
                    'language_id'   => $language->id,
                ]
            );

            Auth::guard('student')->login($student);
            return redirect('/student');

        } catch (IdentityProviderException $e) {
            \Log::error('OAuth IdentityProviderException (student): ' . json_encode($e->getResponseBody()));
            return response('HEMIS autentifikatsiya xatoligi: ' . $e->getMessage(), 500);

        } catch (\UnexpectedValueException $e) {
            // "did not contain a JSON body" — shu yerda ushlanadi
            \Log::error('OAuth JSON xatolik (student): ' . $e->getMessage());
            return response('HEMIS server noto\'g\'ri javob qaytardi. Qayta urinib ko\'ring.', 500);

        } catch (\Exception $e) {
            \Log::error('OAuth umumiy xatolik (student): ' . $e->getMessage());
            return response('Kutilmagan xatolik: ' . $e->getMessage(), 500);
        }
    }
}
