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
    public function data()
    {
        $departments = Department::where('structure', '12')->get()->pluck('id')->toArray();
        foreach ($departments as $department) {
            $page = 1;
            do {
                $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/employee-list', [
                    'type' => 'all', '_department' => $department, 'limit' => 200, 'page' => $page
                ]);
                if ($response->failed()) break;
                $resData = $response->json();
                $items = $resData['data']['items'] ?? [];
                foreach ($items as $item) {
                    User::firstOrCreate([
                        'id' => $item['id'],
                        'name' => json_encode([
                            'full_name' => $item['full_name'],
                            'second_name' => $item['second_name'],
                            'third_name' => $item['third_name'],
                            'short_name' => $item['short_name'],
                        ]),
                        'hemis_id' => $item['employee_id_number'],
                        'current_role' => 'teacher',
                        'hemis_roles' => json_encode(['teacher']),
                        'picture' => $item['image_full'],
                    ]);
                    Workplace::firstOrCreate([
                        'user_id' => $item['id'],
                        'department_id' => $item['department']['id'],
                        'head_type' => $department['staffPosition']['code'] == '16' ? 'department' : 'user',
                        'is_main' => $department['employmentForm']['code'] == '11' ? '1' : '0',
                    ]);
                }
                $pageCount = $resData['data']['pagination']['pageCount'] ?? 1;
                $page++;
            } while ($page <= $pageCount);
        }
    }

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
            'clientId' => env('HEMIS_CLIENT_ID'),
            'clientSecret' => env('HEMIS_CLIENT_SECRET'),
            'redirectUri' => env('HEMIS_REDIRECT_URI_USER'),
            'urlAuthorize' => env('HEMIS_USER_URL') . '/oauth/authorize',
            'urlAccessToken' => env('HEMIS_USER_URL') . '/oauth/access-token',
            'urlResourceOwnerDetails' => env('HEMIS_USER_URL') . '/oauth/api/user?fields=id,uuid,employee_id_number,type,roles,name,login,email,picture,firstname,surname,patronymic,birth_date,university_id,phone'
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
            foreach ($user_array['roles'] as $role) $roles[] = $role['code'];
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
            return redirect('/home');
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
    public
    function student(Request $request)
    {
        return redirect()->back();

        $employeeProvider = new GenericProvider([
            'clientId' => env('HEMIS_CLIENT_ID'),
            'clientSecret' => env('HEMIS_CLIENT_SECRET'),
            'redirectUri' => env('HEMIS_REDIRECT_URI_STUD'),
            'urlAuthorize' => env('HEMIS_STUD_URL') . '/oauth/authorize',
            'urlAccessToken' => env('HEMIS_STUD_URL') . '/oauth/access-token',
            'urlResourceOwnerDetails' => env('HEMIS_STUD_URL') . '/oauth/api/user?fields=id,uuid,employee_id_number,type,roles,name,login,email,picture,firstname,surname,patronymic,birth_date,university_id,phone'
        ]);

        // Handle OAuth authorization
        $authResponse = $this->handleOAuthAuthorization($request, $employeeProvider, '/login/student/');
        if ($authResponse) {
            return $authResponse;
        }

        try {
            $accessToken = $employeeProvider->getAccessToken('authorization_code', [
                'code' => $request->code
            ]);
            $resourceOwner = $employeeProvider->getResourceOwner($accessToken);
            $student_array = $resourceOwner->toArray();
            $uuid = $student_array['uuid'];
            $student_id = $student_array['id'];
            $student_array = $student_array['data'];

            // Create or update related entities
            Department::firstOrCreate(
                ['id' => $student_array['faculty']['id']],
                [
                    'name' => $student_array['faculty']['name'],
                    'structure' => $student_array['faculty']['structureType']['code'],
                ]
            );

            $specialty = Specialty::firstOrCreate(
                [
                    'uuid' => $student_array['specialty']['id'],
                    'department_id' => $student_array['faculty']['id'],
                ],
                [
                    'name' => $student_array['specialty']['name'],
                    'code' => $student_array['specialty']['code'],
                ]
            );

            Level::firstOrCreate(
                ['id' => $student_array['level']['code']],
                ['name' => $student_array['level']['name']]
            );

            Language::firstOrCreate(
                ['id' => $student_array['group']['educationLang']['code']],
                ['name' => $student_array['group']['educationLang']['name']]
            );

            Course::firstOrCreate(
                ['id' => $student_array['group']['id']],
                [
                    'name' => $student_array['group']['name'],
                    'specialty_id' => $specialty->id,
                    'language_id' => $student_array['group']['educationLang']['code'],
                ]
            );

            // Create or update student
            $student = Student::firstOrCreate(
                ['id' => $student_array['student_id_number']],
                [
                    'name' => json_encode([
                        'first_name' => $student_array['first_name'],
                        'second_name' => $student_array['second_name'],
                        'third_name' => $student_array['third_name'],
                        'full_name' => $student_array['full_name'],
                        'short_name' => $student_array['short_name'],
                    ]),
                    'uuid' => $uuid,
                    'student_id' => $student_id,
                    'picture' => $student_array['image'],
                ]
            );

            // Create or update student course relationship
            StudentCourse::firstOrCreate(
                [
                    'student_id' => $student_array['student_id_number'],
                    'level_id' => $student_array['level']['code'],
                ],
                ['course_id' => $student_array['group']['id']]
            );

            Auth::guard('student')->login($student);
            return redirect('/home');
        } catch (IdentityProviderException $e) {
            return response($e->getMessage(), 500);
        }
    }
}
