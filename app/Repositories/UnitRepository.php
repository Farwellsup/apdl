<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Unit;

class UnitRepository extends ModuleRepository
{
    use HandleSlugs, HandleRevisions;

    public function __construct(Unit $model)
    {
        $this->model = $model;
    }

    public function create(array $fields): TwillModelContract
    {
        return DB::transaction(function () use ($fields) {
            $original_fields = $fields;

        
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



    public function update(int|string $id, array $fields): TwillModelContract
    {
        return DB::transaction(function () use ($id, $fields) {
            $model = $this->model->findOrFail($id);

            $original_fields = $fields;

            $this->beforeSave($model, $fields);

            $fields = $this->prepareFieldsBeforeSave($model, $fields);

            $model->fill(Arr::except($fields, $this->getReservedFields()));


            $model->save();

            $this->afterSaveOriginalData($model, $original_fields);

            $this->afterSave($model, $fields);

            return $model->fresh();
        }, 3);
    }
}
