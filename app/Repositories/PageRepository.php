<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Arr;
use App\Models\Page;
use App\Models\Menu;
use DB;

class PageRepository extends ModuleRepository
{
    use HandleBlocks, HandleSlugs, HandleMedias, HandleFiles, HandleRevisions;

    const PAGE_HOMEPAGE = 'home';

    public function __construct(Page $model)
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

            $model->save();

            $menu = Menu::where('key', '=', $model->key)->first();


            if (empty($menu)) {
                $create = new Menu();
                $create->title = $model->title;
                $create->key = $model->key;
                $create->menu_type_id = $fields['menu_type'];
                $create->published = true;
                $create->save();
            } else {
                $update = Menu::where('key', '=', $model->key)->first();
                $update->title = $model->title;
                $update->key = $model->key;
                $update->menu_type_id = $fields['menu_type'];
                $update->published = true;
                $update->save();
            }

            $this->afterSaveOriginalData($model, $original_fields);

            $this->afterSave($model, $fields);

            return $model->fresh();
        }, 3);
    }


    public function updateBasic(int|string|null|array $id, array $values, array $scopes = []): bool
    {


        return DB::transaction(function () use ($id, $values, $scopes) {
            // apply scopes if no id provided
            if (is_null($id)) {
                $query = $this->model->query();

                foreach ($scopes as $column => $value) {
                    $query->where($column, $value);
                }

                $query->update($values);

                $query->get()->each(function ($object) use ($values) {
                    $this->afterUpdateBasic($object, $values);
                });


                return true;
            }

            // apply to all ids if array of ids provided
            if (is_array($id)) {
                $query = $this->model->whereIn('id', $id);
                $query->update($values);

                $query->get()->each(function ($object) use ($values) {
                    $this->afterUpdateBasic($object, $values);
                });

                return true;
            }

            if (($object = $this->model->find($id)) != null) {

                $object->update($values);
                $this->afterUpdateBasic($object, $values);

                $update = Menu::where('key', '=', $object->key)->first();

                if ($object->published) {

                    $update->published = true;
                    $update->active = 1;
                    $update->save();
                } else {
                    $update->published = false;
                    $update->active = 0;
                    $update->save();
                }
                return true;
            }

            return false;
        }, 3);
    }

    public function getPage($key, $with = [])
    {
        return $this->getByKey($key, $with);
    }
    public function getByKey($key, $with = [])
    {
        if (
            ($page = $this->model
                ->with($with)
                ->where('key', $key)
                ->first()) == null
        ) {
            if (isset($this->defaultTitle[$key])) {
                $title = $this->defaultTitle[$key];
            } else {
                $title = 'Untitled';
            }

            $page = Page::create([
                'key' => $key,
                'title' => $title,
                'published' => 1,
                'active' => 1
            ]);
        }
        return $page;
    }
}
