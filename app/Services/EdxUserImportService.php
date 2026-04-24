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

class EdxUserImportService
{
    public function handle(array $rows): array|string
    {

        $lastIdFile = storage_path('app/last_id_student_edx.txt');
        $lastId = file_exists($lastIdFile) ? (int) file_get_contents($lastIdFile) : 0;
        // dd($lastId);
        $latestRecord = EdxAuthUser::orderBy('id', 'desc')->first();

        $latestId = $latestRecord->id ?? 0;

         $total = EdxAuthUser::count();

        $token = JwtHelper::generateToken();

        $url = 'https://transform.thebrandinside.com/api/edxUsers?lastId=' . $latestId;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("$url");


        try {
            if ($response->successful()) {
                $data = collect($response->json())
                    ->reverse()  // newest first
                    ->values()       // reset array keys for upsert
                    ->toArray();     // convert to plain array

                $apiCount = count($data);

                foreach (array_chunk($data, 500) as $chunk) {

                    foreach ($rows as $user) {

                        if ($chunk) {
                            $edxUsers = array_map(function ($item) use ($user) {

                                if ($item['username'] === $user['username']) {
                                    return [
                                        'id'                         => $item['id'],
                                        'password'                   => $user['password'],
                                        'is_superuser'               => $item['is_superuser'],
                                        'username'                   => $item['username'],
                                        'first_name'                 => ($item['username']) ? $user['username'] : $user['first_name'],
                                        'last_name'                  => ($item['username']) ? $user['username'] : $user['first_name'],
                                        'email'                      => $user['email'],
                                        'is_staff'                   => $item['is_staff'],
                                        'is_active'                  => $item['is_active'],
                                        'last_login'                 => $item['last_login']
                                            ? Carbon::parse($item['last_login'])->toDateTimeString()
                                            : null,

                                        'date_joined'               => $item['date_joined']
                                            ? Carbon::parse($item['date_joined'])->toDateTimeString()
                                            : null,

                                    ];
                                }
                            }, $chunk);



                            $edxUserProfiles = array_map(function ($item) use ($user) {
                                if (!empty($item['profile'])) {
                                    if ($item['username'] === $user['username']) {
                                        return [
                                            'id'                       => $item['profile']['id'],
                                            'name'                     => $item['profile']['name'],
                                            'meta'                     => $item['profile']['meta'],
                                            'courseware'               => $item['profile']['courseware'],
                                            'language'                 => $item['profile']['language'],
                                            'location'                 => $item['profile']['location'],
                                            'year_of_birth'            => $item['profile']['year_of_birth'],
                                            'gender'                   => $item['profile']['gender'],
                                            'level_of_education'       => $item['profile']['level_of_education'],
                                            'mailing_address'          => $item['profile']['mailing_address'],
                                            'city'                     => $item['profile']['city'],
                                            'country'                  => $item['profile']['country'],
                                            'goals'                    => $item['profile']['goals'],
                                            'allow_certificate'        => $item['profile']['allow_certificate'],
                                            'bio'                      => $item['profile']['bio'],
                                            'profile_image_uploaded_at'       => $item['profile']['profile_image_uploaded_at'],
                                            'user_id'                   => $item['profile']['user_id'],
                                        ];
                                    }
                                }
                            }, $chunk);


                            $edxUserRegistrations = array_map(function ($item) use ($user) {
                                if (!empty($item['auth_registration'])) {
                                    if ($item['username'] === $user['username']) {
                                        return [
                                            'id'                       => $item['auth_registration']['id'],
                                            'activation_key'           => $item['auth_registration']['activation_key'],
                                            'user_id'                  => $item['auth_registration']['user_id'],
                                        ];
                                    }
                                }
                            }, $chunk);



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
                                $edxUserProfiles,
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
                                    'allow_certificate',
                                    'bio',
                                    'profile_image_uploaded_at',
                                    'user_id',
                                ]
                            );




                            // Perform bulk upsert
                            AuthRegistration::upsert(
                                $edxUserRegistrations,
                                ['id'], // unique key
                                [
                                    // Columns to update if record already exists
                                    'activation_key',
                                    'user_id',
                                ]
                            );
                        }
                    }
                }


                   $total+=$apiCount;
                   file_put_contents($lastIdFile,  $lastId+$apiCount);

                return [
                    'status' => true,
                    'message' => "Successfully imported {$apiCount} users from API.",
                    'total_imported' => $total,
                ];

            } else {
                $statusCode = $response->status();
                $errorMessage = $response->body();
                return [
                    'status' => false,
                    'message' => "API Error: {$statusCode} - {$errorMessage}",
                ];
            }
        } catch (\Exception $e) {

            return [
                'status' => false,
                'message' => "Exception occurred: " . $e->getMessage(),
            ];
        }
    }
}
