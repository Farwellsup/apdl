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
use App\Repositories\AuthenticateEdxRepository;

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
        AuthFactory $authFactory,
        User $model
    ) {
        $userModel = twillModel('user');
        $this->model = $model ?? $userModel;
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

                $resetPd = (App::environment(['local', 'staging'])) ? true :  app(AuthenticateEdxRepository::class)->resetEdxPassword($model, $fields['new_password']);

                dd($resetPd);

                // if ($resetPd !== true) {
                //     //Error, reactivate reset
                //     return redirect()->back()->withErrors("There was a problem resetting the password on the LMS. Please try again later or report to support.");
                // }

                $model->password = Hash::make($fields['new_password']);
                $model->reset_pd = 0;
            }


            $model->save();

            $this->afterSaveOriginalData($model, $original_fields);

            $this->afterSave($model, $fields);

            return $model->fresh();
        }, 3);
    }



}
