<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Department;

class DepartmentRepository extends ModuleRepository
{
    use HandleSlugs, HandleRevisions;

    public function __construct(Department $model)
    {
        $this->model = $model;
    }
}
