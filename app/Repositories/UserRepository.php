<?php

namespace App\Repositories;


use A17\Twill\Repositories\UserRepository as TwillUserRepository;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleOauth;
use A17\Twill\Repositories\Behaviors\HandleUserPermissions;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Passwords\PasswordBrokerManager;

class UserRepository extends TwillUserRepository
{

   use HandleMedias;
    use HandleOauth;
    use HandleUserPermissions;

    protected Config $config;
 


    protected PasswordBrokerManager $passwordBrokerManager;

    public function __construct(
   
        Config $config,
        PasswordBrokerManager $passwordBrokerManager,
        AuthFactory $authFactory
    ) {
        $userModel = twillModel('user');
        $this->model = new $userModel();
        $this->passwordBrokerManager = $passwordBrokerManager;
        $this->authFactory = $authFactory;
        $this->config = $config;
     
    }



    public function update(int|string $id, array $fields): TwillModelContract
    {
        return DB::transaction(function () use ($id, $fields) {

            $model = $this->model->findOrFail($id);

            $original_fields = $fields;

            $this->beforeSave($model, $fields);

            $fields = $this->prepareFieldsBeforeSave($model, $fields);

            $model->fill(Arr::except($fields, $this->getReservedFields()));

            if (Arr::has($fields, 'new_password') && !empty($fields['new_password'])) {

                $resetPd = (App::environment(['local', 'staging'])) ? true :  $this->resetEdxPassword($model, $fields['new_password']);

                if ($resetPd !== true) {
                    //Error, reactivate reset
                    return redirect()->back()->withErrors("There was a problem resetting the password on the LMS. Please try again later or report to support.");
                }

                $model->password = Hash::make($fields['new_password']);
                $model->reset_pd = 0;
            }


            $model->save();

            $this->afterSaveOriginalData($model, $original_fields);

            $this->afterSave($model, $fields);

            return $model->fresh();
        }, 3);
    }



    public function resetEdxPassword($user, $password)
    {
        $configLms = config()->get("settings.lms.live");
        $configApp = config()->get("settings.app");

        //if password reset, perform edx password resets
        $email = $user->email;
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $configLms['LMS_RESET_PASSWORD_PAGE']);

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
