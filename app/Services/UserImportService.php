<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\TwillPosition;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UploadedUsers;
use App\Edx\EdxAuthUser;
use Ixudra\Curl\Facades\Curl;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use App;
use App\Repositories\AuthenticateEdxRepository;

class UserImportService
{
    public function handle(array $rows, int $companyId, string $fileName): string
    {
        $company = Company::with(['departments', 'units'])
            ->findOrFail($companyId);


        // preload relationships
        $departments = $company->departments->keyBy('title');
        $units = $company->units->keyBy('title');

        // preload users
        $existingUsers = User::where('company_id', $company->id)
            ->pluck('id', 'payroll_number');

        $data = [];

        foreach (array_chunk($rows, 300) as $chunk) {
            foreach ($chunk as $row) {
                // process

                if (empty($row['payroll_number'])) {
                    continue;
                }


                $parts = array_values(array_filter(explode(' ', $row['name'] ?? '')));

                $first_name = $parts[0] ?? '';
                $last_name = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

                $department = $departments[$row['department']] ?? null;
                $unit = $units[$row['unit']] ?? null;

                $payroll = $row['payroll_number'];

                $email = $payroll . '@' . str_replace(' ', '-', $company->title) . '.com';

                $password = Str::random(9);

                // collect credentials for export
                $data[] = [
                    'name' => $row['name'],
                    'payroll_number' => $payroll,
                    'password' => $password,
                ];



                // create user only if not exists
                if (!isset($existingUsers[$payroll])) {

                    $user = User::create([
                        'name' => $row['name'],
                        'email' => $email,
                        'password' => Hash::make($password),
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'payroll_number' => $payroll,
                        'company_name' => $company->title,
                        'company_id' => $company->id,
                        'username' => ($row['username']) ? $row['username'] : $payroll,
                        'department_id' => $department?->id,
                        'department_name' => $department?->title,
                        'unit_id' => $unit?->id,
                        'unit_name' => $unit?->title,
                        'role_id' => 5,
                        'published' => 1,
                        'email_verified_at' => now(),
                        'registered_at' => now()
                    ]);


                    $existingUsers[$payroll] = $user->id;

                    TwillPosition::create([
                        'user_id' => $user->id,
                        'position' => encrypt($password),
                    ]);

                    if (App::environment(['local', 'staging'])) {
                        return true;
                    } else {

                        $edxUser = EdxAuthUser::where('username', $user->username)->first();

                        if (!$edxUser) {

                            $register = app(AuthenticateEdxRepository::class)->edxRegister($user, $password);

                            if ($register !== true) {
                                // log error
                                \Log::error("Failed to register user {$user->email} on edX:");
                                // \Log::error("Failed to register user {$user->email} on edX: {json_encode($register)}"); --- IGNORE ---
                            }
                        } else {
                            \Log::info("User {$user->email} already exists on edX");

                            (App::environment(['local', 'staging'])) ? true : app(AuthenticateEdxRepository::class)->resetEdxPassword($user, $password);
                            // app(AuthenticateEdxRepository::class)->resetEdxPassword($user, $password); --- IGNORE ---
                        }
                    }
                } else {
                    

                    if (App::environment(['local', 'staging'])) {

                        return true;


                    } else {

                        $user = User::find($existingUsers[$payroll]);

                        $edxUser = EdxAuthUser::where('username', $user->username)->first();

                        if (!$edxUser) {

                            $register = app(AuthenticateEdxRepository::class)->edxRegister($user, $password);

                            if ($register !== true) {
                                // log error
                                \Log::error("Failed to register user {$user->email} on edX:");
                                // \Log::error("Failed to register user {$user->email} on edX: {json_encode($register)}"); --- IGNORE ---
                            }
                        } else {
                            \Log::info("User {$user->email} already exists on edX");

                            (App::environment(['local', 'staging'])) ? true : app(AuthenticateEdxRepository::class)->resetEdxPassword($user, $password);
                            // app(AuthenticateEdxRepository::class)->resetEdxPassword($user, $password); --- IGNORE ---
                        }
                    }
                }
            }
        }

        // store export
        Excel::store(new UploadedUsers($data), 'exports/' . $fileName, 'public');

        return $fileName;
    }

    public function registerEdx($user, $password)
    {
        $configLms = config()->get("settings.lms.live");

        //Validate user
        if ($user === null) {
            return;
        }
        //Package data to be sent
        if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $email = $user->email;
        }

        $data = [
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->payroll_number,
            'honor_code' => 'true',
            'password' => $password,
            'country' => 'KE',
            'terms_of_service' => 'true',
            'active' => 'true',
        ];

        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'cache-control' => 'no-cache',
            'Referer' => $configLms['APP_URL'] . '/register',
        );

        $client = new \GuzzleHttp\Client();

        try {

            $response = $client->request(
                'POST',
                $configLms['LMS_REGISTRATION_URL'],
                [
                    'form_params' => $data,
                    'headers' => $headers,
                ]
            );

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            $responseJson = $e->getResponse();
            $response = json_decode($responseJson->getBody()->getContents(), true);

            $errors = [];
            foreach ($response as $key => $error) {
                //Return error
                $errors[] = $error;
            }
            //echo "CATCH 1";

            return $errors[0];
        } catch (\Exception $e) {


            return $e->getMessage();
        }
    }
}
