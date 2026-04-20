<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\UserController as TwillUserController;
use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use Illuminate\Http\Request;
use App\Repositories\CompanyRepository;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use A17\Twill\Services\Forms\Fields\BaseFormField;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Fields\Repeater;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\Services\Forms\Fields\Files;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Models\Contracts\TwillModelContract;
use App\Http\Requests\Twill\UploadUsers;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends TwillUserController
{
    //
    protected $namespace = 'App';
    protected $moduleName = 'users';
    protected $titleColumnKey = 'name';
    protected $indexOptions = [
        'editInModal' => false,
        'skipCreateModal' => true,
        'includeScheduledInList' => true,
        'showImage' => false,
        'sortable' => true,
    ];


    /**
     * @return IlluminateView|JsonResponse
     */
    public function index(?int $parentModuleId = null): mixed
    {
        $this->authorizeOption('list', $this->moduleName);

        $parentModuleId = $this->getParentModuleIdFromRequest($this->request) ?? $parentModuleId;

        $this->submodule = isset($parentModuleId);
        $this->submoduleParentId = $parentModuleId;

        $indexData = $this->getIndexData(
            $this->submodule ? [
                $this->getParentModuleForeignKey() => $this->submoduleParentId,
            ] : []
        );

        if ($this->request->ajax() || $this->request->expectsJson()) {
            return new JsonResponse($indexData + ['replaceUrl' => true]);
        }

        if ($this->request->has('openCreate') && $this->request->get('openCreate')) {
            $indexData += ['openCreate' => true];
        }

        $form = $this->getCreateForm();

        if (
            $form->filter(function (BaseFormField $field) {
                return $field instanceof BlockEditor ||
                    $field instanceof Repeater;
            })
            ->isNotEmpty()
        ) {
            throw new \Exception('Create forms do not support repeaters and blocks');
        }

        if ($form->isNotEmpty()) {
            $view = 'twill.users.index';
        } else {
            $view = Collection::make([
                "$this->viewPrefix.index",
                "twill::$this->moduleName.index",
                'twill.users.index',
            ])->first(function ($view) {
                return View::exists($view);
            });
        }



        return View::make($view, $indexData + ['repository' => $this->repository])
            ->with(['formBuilder' => $form->toFrontend(isCreate: true)]);
    }

    


    protected function indexData($request)
    {
        return [
            'defaultFilterSlug' => 'activated',
            'create' => $this->getIndexOption('create') && $this->user->can('edit-users'),
            'companyList' => app(CompanyRepository::class)->listAll('title'),
        ];
    }


    public function uploadForm(){
       
    if (hasRole(['Owner', 'Administrator'])) {
        $companyList = Company::pluck('title', 'id');

    } elseif (hasRole(['Company HR'])) {
        $companyList = Company::where('id', Auth::user()->company_id)->pluck('title', 'id');
    }

    return view('twill.users.uploadUsers', compact('companyList'));
      
    }


    public function uploadStore(UploadUsers $request)
    {

      try {
            // Ensure file is uploaded
            if (!request()->hasFile('user_list')) {
                return redirect()->back()->withErrors('Please upload a file.');
            }

            Excel::import(new UserImport($request->company_id), request()->file('user_list'));

            // $status = Session::get('deactivated');

            // switch ($status) {
            //     case 'Yes':
            //         Session::flash('status', 'Users restored successfully.');
            //         return redirect()->route('admin.users.index');

            //     case 'Empty':
            //         return redirect()->back()->withErrors('Uploaded file is empty.');

            //     case 'No':
            //         return redirect()->back()->withErrors('File must contain a column labeled "email_address" as the first and only column');

            //     case 'Active':
            //         return redirect()->back()->withErrors('Users are already active.');

            //     case 'NoMatches':
            //         return redirect()->back()->withErrors('Kindly confirm your file contains trashed users');

            //     default:
            //         return redirect()->back()->withErrors('Unexpected error occurred during restoration.');
            // }
        } catch (\Throwable $e) {
            dd($e);
            \Log::error('User restore import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withErrors('Something went wrong. Please try again.');
        }






    //   if(request()->file('user_list')){

    //     Excel::import(new UserImport, request()->file('user_list'));

        
    //   }

    dd($request);

        // Excel::import(new LicenseImport, request()->file('file'));

        // $name = Session::get('uploaded');

        // if ($name === 'Yes') {

        //     Session::flash('status', 'Import Successful');

        //     return redirect()->route('admin.accounts.allowedAccounts.index');
        // }
    }

  


    public function store($parentModuleId = null)
    {




        $this->authorizeOption('create', $this->moduleName);

        $parentModuleId = $this->getParentModuleIdFromRequest($this->request) ?? $parentModuleId;

        $input = $this->request->all();

          
        dd($input);


        $optionalParent = $parentModuleId ? [$this->getParentModuleForeignKey() => $parentModuleId] : [];

        if (isset($input['cmsSaveType']) && $input['cmsSaveType'] === 'cancel') {
            return $this->respondWithRedirect(
                moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'create'
                )
            );
        }


        $item = $this->repository->create($input + $optionalParent);

        activity()->performedOn($item)->log('created');

        $this->fireEvent($input);

        Session::put($this->moduleName . '_retain', true);

        if ($this->getIndexOption('editInModal')) {
            return $this->respondWithSuccess(twillTrans('twill::lang.publisher.save-success'));
        }

        if (isset($input['cmsSaveType']) && Str::endsWith($input['cmsSaveType'], '-close')) {
            return $this->respondWithRedirect($this->getBackLink());
        }

        if (isset($input['cmsSaveType']) && Str::endsWith($input['cmsSaveType'], '-new')) {
            return $this->respondWithRedirect(
                moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'create'
                )
            );
        }

        return $this->respondWithRedirect(
            moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'edit',
                [Str::singular(last(explode('.', $this->moduleName))) => $this->getItemIdentifier($item)]
            )
        );
    }
}
