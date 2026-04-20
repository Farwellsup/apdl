<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\Services\Forms\Form;
use Illuminate\Http\Request;
use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use App\Repositories\CompanyRepository;

class UnitController extends BaseModuleController
{
    protected $moduleName = 'units';

    protected $defaultOrders = [
        'title' => 'asc',
    ];

    protected $defaultIndexOptions = [
        'create' => true,
        'edit' => true,
        'publish' => true,
        'bulkPublish' => true,
        'feature' => false,
        'bulkFeature' => false,
        'restore' => true,
        'bulkRestore' => true,
        'forceDelete' => true,
        'bulkForceDelete' => true,
        'delete' => true,
        'duplicate' => false,
        'bulkDelete' => true,
        'reorder' => false,
        'permalink' => true,
        'bulkEdit' => true,
        'editInModal' => true,
        'skipCreateModal' => false,
        'includeScheduledInList' => true,
        'showImage' => false,
        'sortable' => true,
    ];

     protected $filters = [
        'company_id' => 'company_id',
     ];


       protected function indexData($request)
    {
        return [
            'company_idList' =>   app(CompanyRepository::class)->listAll('title'),
        
        ];
    }

     /**
     * This method can be used to enable/disable defaults. See setUpController in the docs for available options.
     */
    protected function setUpController(): void
    {
        $this->disablePermalink();
    }

   

    
    /**
     * This is an example and can be removed if no modifications are needed to the table.
     */
    protected function additionalIndexTableColumns(): TableColumns
    {
        $table = parent::additionalIndexTableColumns();

        $table->add(
            Text::make()->field('parent_value')->title('Company')
        );

         $table->add(
            Text::make()->field('department_value')->title('Department')
        );

        return $table;
    }


    public function getDepartments($id)
    {
        return \App\Models\Department::where('company_id', $id)
            ->select('id', 'title')
            ->get();
    }

    
}
