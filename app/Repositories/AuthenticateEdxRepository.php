<?php

namespace App\Repositories;

use A17\Twill\Repositories\UserRepository as TwillUserRepository;
use Ixudra\Curl\Facades\Curl;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Log;

class AuthenticateEdxRepository extends TwillUserRepository
{


    public function edxLogin($user, $password)
    {

        $configLms = config()->get("settings.lms.live");
        $configApp = config()->get("settings.app");

        $email = $user->email;
        $password = $password;

        

        //Get CSRF Token

        $client = new \GuzzleHttp\Client(['verify' => $configApp['VERIFY_SSL']]);

        $response = $client->request('GET', $configLms['LMS_LOGIN_URL']);
       
            
        $csrfToken = null;
        foreach ($response->getHeader('Set-Cookie') as $key => $cookie) {
            if (strpos($cookie, 'csrftoken') === false) {
                continue;
            }
            $csrfToken = explode('=', explode(';', $cookie)[0])[1];
            break;
        }

       

        if (!$csrfToken) {
            //Error, reactivate reset
            return false;
        }

        $data = [
            'email' => $email,
            'password' => $password,
        ];

  

        $headers = [
            'Content-Type' => ' application/x-www-form-urlencoded ; charset=UTF-8',
            'Accept' => ' text/html,application/json',
            'X-CSRFToken' => $csrfToken,
            'Cookie' => ' csrftoken=' . $csrfToken,
            'Origin' => $configLms['LMS_BASE'],
            'Referer' => $configLms['LMS_BASE'] . '/login',
            'X-Requested-With' => ' XMLHttpRequest',
        ];
        $client = new \GuzzleHttp\Client(['verify' => $configApp['VERIFY_SSL']]);
        $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'csrftoken' => $csrfToken
            ],
            $configLms['LMS_DOMAIN']
        );

        $response = $client->request(
            'POST',
            $configLms['LMS_LOGIN_URL'],
            [
                'form_params' => $data,
                'headers' => $headers,
                'cookies' => $cookieJar
            ]
        );

         
        //set cookies

      

        if (!$response->hasHeader('Set-Cookie')) {
            return false;
        }


        $loggedInCookies = $response->getHeader('Set-Cookie');

     

        $setCookies = [];
        foreach ($loggedInCookies as $userCookie) {
            //format cookies
            $cookieDetails = (explode(';', $userCookie));

            $ourCookie = [];
            foreach ($cookieDetails as $cookieDetail) {


                $key = strtolower(trim(explode('=', $cookieDetail)[0]));

                $value = isset(explode('=', $cookieDetail)[1]) ? trim(explode('=', $cookieDetail)[1]) : 1;


                if (in_array(strtolower($key), ['__cfduid', 'csrftoken', 'edxloggedin', 'sessionid', 'openedx-language-preference'])) {
                    $ourCookie['name'] = $key;
                    $ourCookie['value'] = $value;
                } else {
                    $ourCookie[$key] = $value;
                }
            }
            // var_dump($ourCookie);



            if (isset($ourCookie['name'])) {
                if ($ourCookie['name'] != 'edx-user-info' && !isset($ourCookie['domain'])) {
                    if ($ourCookie['name'] == 'csrftoken') {
                        $ourCookie['domain'] = '';
                    } else {
                        $ourCookie['domain'] = '.' . $configApp['APP_DOMAIN'];
                    }
                    //Set the cookie

                }
                if ($ourCookie['domain'] == $configLms['COOKIE_DOMAIN']) {

                    $ourCookie['domain'] = '.' . $configApp['APP_DOMAIN'];
                }

                $setCookies[] = ['name' => $ourCookie['name'], 'value' => $ourCookie['value'], 'domain' => $ourCookie['domain']];
            }
        }


       

        $data = [
            'grant_type' => 'password',
            'client_id' => $configLms['EDX_KEY'],
            'client_secret' => $configLms['EDX_SECRET'],
            'username' => $user->username,
            'password' => $password,
            // 'token_type'=>'jwt',
        ];



        $tokenUrl = $configLms['LMS_BASE'] . '/oauth2/access_token/';
        //Get authorization token
        $accessResponse = Curl::to($tokenUrl)
            ->withData($data)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();


        if ($accessResponse->status !== 200) {
            return false;
        }
        //Set access token
        $accessToken = json_decode($accessResponse->content, true);

         

        $setCookies[] = ['name' => 'edinstancexid', 'value' => $accessToken['access_token'], 'expiry' => $accessToken['expires_in']];
        $setCookies[] = ['name' => 'edx-company-info', 'value' => $user->company_id, 'expiry' => $accessToken['expires_in']];

 

        foreach ($setCookies as $cookie) {
            $cookie['expiry'] = 0;
            if (!isset($cookie['domain'])) {
                $cookie['domain'] = '.' . $configApp['APP_DOMAIN'];
            }
            setrawcookie($cookie['name'], $cookie['value'], $cookie['expiry'], '/', $cookie['domain']);
        }

        return true;
    }



    public function edxRegister($user, $pass)
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
            'username' => $user->username,
            'honor_code' => 'true',
            'password' => $pass,
            'country' => 'KE',
            'terms_of_service' => 'true',

        ];

        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'cache-control' => 'no-cache',
            'Referer' => env('APP_URL') . '/register',
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


    public function resetEdxPassword($user, $password)
    {
        $configLms = config()->get("settings.lms.live");
        $configApp = config()->get("settings.app");

        //if password reset, perform edx password resets
        $email = $user->email;
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $configLms['LMS_RESET_PASSWORD_PAGE']);
        //var_dump($response);die;
        $csrfToken = null;
        foreach ($response->getHeader('Set-Cookie') as $key => $cookie) {
            if (strpos($cookie, 'csrftoken') === FALSE) {
                continue;
            }
            $csrfToken = explode('=', explode(';', $cookie)[0])[1];
            break;
        }

        if (!$csrfToken) {
            //Error, reactivate reset
            Toastr::error("There was a problem logging you in. Please try again later or report to support.");
            return;
        }

        $data = [
            'email' => $email,
            'password' => $password,
            'csrfmiddlewaretoken' => $csrfToken
        ];


        $headers = [
            'Content-Type' => ' application/x-www-form-urlencoded ; charset=UTF-8',
            'Accept' => ' text/html,application/json',
            'X-CSRFToken' => $csrfToken,
            'Cookie' => ' csrftoken=' . $csrfToken,
            'Origin' => $configLms['LMS_BASE'],
            'Referer' => $configLms['LMS_BASE'],
            'X-Requested-With' => ' XMLHttpRequest',
        ];

        $client = new \GuzzleHttp\Client(['verify' => $configApp['VERIFY_SSL']]);

        $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray([
            'csrftoken' => $csrfToken
        ], $configLms['LMS_DOMAIN']);

        $client = new \GuzzleHttp\Client();

        try {

            $response = $client->request('POST', $configLms['LMS_RESET_PASSWORD_API_URL'], [
                'form_params' => $data,
                'headers' => $headers,
                'cookies' => $cookieJar
            ]);

            //  var_dump($response);die;

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseJson = $e->getResponse();
            $response = json_decode($responseJson->getBody()->getContents(), true);
            //Error, delete user
            return $response;
        } catch (\Exception $e) {
            //Error, reactivate reset
            return $e->getMessage();
            // return redirect()->back()->withErrors("There was a problem resetting your password. Please try again later or report to support.");
        }
    }
}
