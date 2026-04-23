<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\PlatformSetting;
use App\Services\ThemeService;

class PlatformSettingRepository extends ModuleRepository
{
    use HandleSlugs, HandleMedias, HandleFiles, HandleRevisions;

    public function __construct(PlatformSetting $model)
    {
        $this->model = $model;
    }

    public function update(int|string $id, array $fields): TwillModelContract
    {
        return DB::transaction(function () use ($id, $fields) {
            $model = $this->model->findOrFail($id);

            $original_fields = $fields;

            $this->beforeSave($model, $fields);

            $fields = $this->prepareFieldsBeforeSave($model, $fields);

            $model->fill(Arr::except($fields, $this->getReservedFields()));

            $model->theme_css_path = app(ThemeService::class)->generateThemeFile($model);

            $model->theme_updated_at = now();

            $model->save();

            $this->afterSaveOriginalData($model, $original_fields);

            $this->afterSave($model, $fields);

            return $model->fresh();
        }, 3);
    }

    
}
