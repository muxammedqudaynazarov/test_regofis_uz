<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Language;
use App\Models\Level;
use App\Models\Specialty;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class HemisController extends Controller
{
    /**
     * Handle OAuth authorization process
     *
     * @param Request $request
     * @param GenericProvider $provider
     * @param string $redirectPath
     * @return RedirectResponse|Response|null
     */
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

            $user = User::firstOrCreate(
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
                    'uuid' => $user_array['uuid'],
                    'picture' => $user_array['picture'],
                ]
            );

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
    public function student(Request $request)
    {
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
