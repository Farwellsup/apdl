<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\TwillPosition;
use App\Edx\AuthUserprofile;
use App\Edx\AuthRegistration;
use App\Edx\EdxAuthUser;
use Illuminate\Support\Facades\Http;
use App\Helpers\JwtHelper;
use Carbon\Carbon;
use App\Repositories\AuthenticateEdxRepository;

class EdxUserImportService
{
    public function handle(array $rows): array|string
    {

        $lastIdFile = storage_path('app/last_id_student_edx.txt');
        $lastId = file_exists($lastIdFile) ? (int) file_get_contents($lastIdFile) : 0;
        // dd($lastId);
        // $latestRecord = EdxAuthUser::orderBy('id', 'desc')->first();

        // $latestId = $latestRecord->id ?? 0;

        $total = EdxAuthUser::count();

        // $total = 0;

        $token = JwtHelper::generateToken();



        try {


            foreach ($rows as $user) {

                $url = 'https://transform.thebrandinside.com/api/edxUsers/' . $user['username'];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])->get("$url");

                if ($response->successful()) {
                    $data = collect($response->json())
                        ->reverse()  // newest first
                        ->values()       // reset array keys for upsert
                        ->toArray();  // Process the data as needed
                    foreach ($data as $item) {

                        $edxUsers = [
                            'id' => $item['id'],
                            'password' => $item['password'],
                            'is_superuser' => $item['is_superuser'],
                            'username' => $item['username'],
                            'first_name' => ($item['username']) ? $user['username'] : $user['first_name'],
                            'last_name' => ($item['username']) ? $user['username'] : $user['first_name'],
                            'email' => $user['email'],
                            'is_staff' => $item['is_staff'],
                            'is_active' => $item['is_active'],
                            'last_login' => Carbon::parse($item['last_login']),
                            'date_joined' => Carbon::parse($item['date_joined']),
                        ];



                        EdxAuthUser::upsert(
                            $edxUsers,
                            ['id'], // unique key
                            [
                                // Columns to update if record already exists
                                'password',
                                'is_superuser',
                                'username',
                                'first_name',
                                'last_name',
                                'email',
                                'is_staff',
                                'is_active',
                                'last_login',
                                'date_joined',
                            ]
                        );


                        // Perform bulk upsert
                        AuthUserprofile::upsert(
                            $item['profile'],
                            ['id'], // unique key
                            [
                                // Columns to update if record already exists
                                'name',
                                'meta',
                                'courseware',
                                'language',
                                'location',
                                'year_of_birth',
                                'gender',
                                'level_of_education',
                                'mailing_address',
                                'city',
                                'country',
                                'goals',
                                'bio',
                                'profile_image_uploaded_at',
                                'user_id',
                            ]
                        );

                        // Perform bulk upsert
                        AuthRegistration::upsert(
                            $item['auth_registration'],
                            ['id'], // unique key
                            [
                                // Columns to update if record already exists
                                'activation_key',
                                'user_id',
                            ]
                        );


                        $rUser = User::where('email', $user['email'])->first();

                        $reset = app(AuthenticateEdxRepository::class)->resetEdxPassword($rUser, $user['password']);

                        if ($reset !== true) {
                            // log error
                            \Log::error("Failed to reset password for user {$user['email']} on edX: " . json_encode($reset));
                            // \Log::error("Failed to reset password for user {$user['email']} on edX: {json_encode($reset)}"); --- IGNORE ---
                            return [
                                'status' => false,
                                'message' => "Failed to reset password for user {$user['email']} on edX: " . json_encode($reset),
                            ];
                        }

                        $total += 1;
                        file_put_contents($lastIdFile,  $lastId + $total);
                    }


                    return [
                        'status' => true,
                        'message' => "Successfully imported {$total} users from API.",
                        'total_imported' => $total,
                    ];
                } else {
                    // Handle the error response
                    return [
                        'status' => false,
                        'message' => "Failed to fetch data for username: {$user['username']}. Status: {$response->status()}",
                    ];
                }
            }
        } catch (\Exception $e) {

            return [
                'status' => false,
                'message' => "Exception occurred: " . $e->getMessage(),
            ];
        }
    }
}
