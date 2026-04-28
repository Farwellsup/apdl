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
use App\Models\Company;
use App\Models\Department;
use App\Models\Unit;
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


    public function create(array $fields): TwillModelContract
    {
        return DB::transaction(function () use ($fields) {

            $original_fields = $fields;
            $company = Company::findOrFail($fields['company_id']);

            $this->beforeSave($this->model, $fields);

            $fields = $this->prepareFieldsBeforeSave($this->model, $fields);

            $fields['name'] = $fields['first_name'] . ' ' . $fields['last_name'];
            $fields['email'] = $fields['email'] ?? ($fields['payroll_number'] . '@' . str_replace(' ', '-', $company->title) . '.com');
            $fields['company_name'] = $company->title;
            $fields['department_name'] = $fields['department_id'] ? Department::find($fields['department_id'])->title : null;
            $fields['unit_name'] = $fields['unit_id'] ? Unit::find($fields['unit_id'])->title : null;
            $fields['username'] = $fields['payroll_number'];
            $fields['role_id'] = 5;
            $fields['published'] = 1;
            $fields['email_verified_at'] = now();
            $fields['registered_at'] = now();

            $model = $this->model->create(Arr::except($fields, $this->getReservedFields()));

            $registered = (App::environment(['local', 'staging'])) ? true :  app(AuthenticateEdxRepository::class)->resetEdxPassword($model, $fields['password']);

            $this->afterSaveOriginalData($model, $original_fields);
            

          

            $model->save();

            $this->afterSave($model, $fields);

            return $model;
        }, 3);
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
