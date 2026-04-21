<?php

namespace App\Observers;

use App\Models\User;
use App;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {

        if (App::environment(['local', 'staging'])) {
            dd('User created: ' . $user->email);
            
            return true;
        }


        if (request()->has('password')) {
            $password = request()->get('password');
        } else {
            return false;
        }

        //
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

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
