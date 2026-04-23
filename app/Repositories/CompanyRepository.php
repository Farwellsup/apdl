<?php

namespace App\Repositories;

use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleFiles;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\Company;


class CompanyRepository extends ModuleRepository
{
    use HandleSlugs, HandleMedias, HandleFiles, HandleRevisions;

    public function __construct(Company $model)
    {
        $this->model = $model;
    }

    
}
