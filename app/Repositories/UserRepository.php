<?php

namespace App\Repositories;

use A17\Twill\Repositories\UserRepository as TwillUserRepository;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\User;



class UserRepository extends TwillUserRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }


     public function create(array $fields): TwillModelContract
    {
        return DB::transaction(function () use ($fields) {
            $original_fields = $fields;

            dd($fields);

            $fields = $this->prepareFieldsBeforeCreate($fields);

            $model = $this->model->make(Arr::except($fields, $this->getReservedFields()));

            $fields = $this->prepareFieldsBeforeSave($model, $fields);

            $model->fill(Arr::except($fields, $this->getReservedFields()));

            $this->beforeSave($model, $original_fields);

            $model->save();

            $this->afterSaveOriginalData($model, $original_fields);

            $this->afterSave($model, $fields);

            return $model;
        }, 3);
    }
}