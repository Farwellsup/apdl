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
use A17\Twill\Enums\PermissionLevel;
use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Services\Listings\Columns\Image;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\Filters\QuickFilter;
use A17\Twill\Services\Listings\Filters\QuickFilters;
use A17\Twill\Services\Listings\TableColumns;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Models\Contracts\TwillModelContract;
use App\Http\Requests\Twill\UploadUsers;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Services\UserImportService;
use App\Jobs\ProcessUserUploadJob;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Country;

use Illuminate\Support\Facades\Auth;


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


    protected function formData($request)
    {
        $currentUser = $this->authFactory->guard('twill_users')->user();

        if (TwillPermissions::levelIs(PermissionLevel::LEVEL_ROLE_GROUP_ITEM)) {
            $permissionsData = [
                'permissionModules' => $this->getPermissionModules(),
            ];
        }

        $isGroupHr = $currentUser->hasRole('Group HR') || $currentUser->role_id === 3;
        $isCompanyHr = $currentUser->hasRole('Company HR') || $currentUser->role_id === 4;

        $companyOptions = collect($isCompanyHr ? Company::published()->where('id', $currentUser->company_id)->pluck('title', 'id')->toArray() : Company::published()->pluck('title', 'id')->toArray())->map(function ($item, $key) {
            return ['value' => $key, 'label' => $item];
        })->values()->toArray();
        $unitOptions = collect($isCompanyHr ? Unit::published()->where('id', $currentUser->company_id)->pluck('title', 'id')->toArray() : Unit::published()->pluck('title', 'id')->toArray())->map(function ($item, $key) {
            return ['value' => $key, 'label' => $item];
        })->values()->toArray();
        $departmentOptions = collect($isCompanyHr ? Department::published()->where('id', $currentUser->company_id)->pluck('title', 'id')->toArray() : Department::published()->pluck('title', 'id')->toArray())->map(function ($item, $key) {
            return ['value' => $key, 'label' => $item];
        })->values()->toArray();
        $countryOptions = collect(Country::published()->pluck('title', 'id')->toArray())->map(function ($item, $key) {
            return ['value' => $key, 'label' => $item];
        })->values()->toArray();


        return [
            'roleList' => $this->getRoleList(),
            'isGroupHr' => $isGroupHr,
            'isCompanyHr' => $isCompanyHr,
            'companyOptions' => $companyOptions,
            'unitOptions' => $unitOptions,
            'departmentOptions' => $departmentOptions,
            'countryOptions' => $countryOptions,
        ] + ($permissionsData ?? []);
    }

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


    private function getRoleList()
    {
        if (config('twill.enabled.permissions-management')) {
            return twillModel('role')::accessible()->published()->get()->map(function ($role) {
                return ['value' => $role->id, 'label' => $role->name];
            })->toArray();
        }

        return collect(TwillPermissions::roles()::toArray())->map(function ($item, $key) {
            return ['value' => $key, 'label' => $item];
        })->values()->toArray();
    }




    public function getIndexTableColumns(): TableColumns
    {
        $tableColumns = TableColumns::make();
        if ($this->config->get('twill.enabled.users-image')) {
            $tableColumns->add(
                Image::make()
                    ->field('image')
                    ->title('Image')
                    ->rounded()
            );
        }

        $tableColumns->add(
            Text::make()
                ->field($this->titleColumnKey)
                ->linkToEdit()
                ->sortable(),
        );

        $tableColumns->add(
            Text::make()
                ->field('payroll_number')
                ->title('Payroll Number')
                ->sortable()
        );


        $tableColumns->add(
            Text::make()
                ->field(twillModel('user')::getRoleColumnName())
                ->title('Role')
                ->customRender(function (TwillModelContract $user) {
                    if (TwillPermissions::enabled()) {
                        return Str::title($user->role->name);
                    }
                    return Str::title($user->role);
                })
                ->sortable()
        );

        $tableColumns->add(
            Text::make()
                ->field('company_name')
                ->title('Company')

        );


        $tableColumns->add(
            Text::make()
                ->field('department_name')
                ->title('Department')

        );

        $tableColumns->add(
            Text::make()
                ->field('unit_name')
                ->title('Unit')
                ->sortable()
        );

        $tableColumns->add(
            Text::make()
                ->field('registered_at')
                ->title('Registered At')
                ->customRender(function (TwillModelContract $user) {
                    return $user->registered_at ? $user->registered_at->format('d M Y, H:i') : '-';
                })
                ->sortable()
        );

        $tableColumns->add(
            Text::make()
                ->field('last_login_at')
                ->title('Last Login')
                ->customRender(function (TwillModelContract $user) {
                    return $user->last_login_at ? $user->last_login_at->ago() : '-';
                })
                ->sortable()
        );

        return $tableColumns;
    }


    public function uploadForm()
    {

        if (hasRole(['Owner', 'Administrator'])) {
            $companyList = Company::pluck('title', 'id');
        } elseif (hasRole(['Company HR'])) {
            $companyList = Company::where('id', Auth::user()->company_id)->pluck('title', 'id');
        }

        return view('twill.users.uploadUsers', compact('companyList'));
    }


    public function uploadStore(UploadUsers $request, UserImportService $userImportService)
    {
        try {

            $company = Company::findOrFail($request->company_id);

            $fileName = 'Africa_Poultry_Development_Learning' . '_' . str_replace(' ', '_', $company->title) . '_user_credentials_' . now()->format('d-M-Y_Hi') . '.xlsx';

            $rows = Excel::toArray(new UserImport, request()->file('user_list'))[0];

            // dispatch job synchronously OR async depending on your need

               $r = $userImportService->handle($rows, $request->company_id, $fileName);

               dd($r);

           // ProcessUserUploadJob::dispatch($rows, $request->company_id, $fileName);


            Session::flash('download_url', $fileName);
            Session::flash('status', 'The User Upload has been initiated. The file will be downloaded once done. Please don\'t close this page.');

            return redirect()->route('twill.users.index')->with('download_url', $fileName);
        } catch (\Throwable $e) {

            \Log::error('User upload failed: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to process the file. Please ensure it is correctly formatted and try again.');
        }
    }

    // Check if file is ready
    public function checkStatus(Request $request)
    {
        $fileName = $request->query('file');
        $filePath = 'exports/' . $fileName;
        $errorsFilePath = 'exports/errors_' . $fileName;

        if (Storage::disk('public')->exists($filePath) || Storage::disk('public')->exists($errorsFilePath)) {
            return response()->json([
                'ready' => true,
                'url' => Storage::disk('public')->url($filePath),
                'errors_url' => Storage::disk('public')->url($errorsFilePath),
            ]);
        } else {
            $filePath = "exports/'" . $fileName . "'";
            $errorsFilePath = "exports/'errors_" . $fileName . "'";

            return response()->json([
                'ready' => Storage::disk('public')->exists($filePath),
                'url' => Storage::disk('public')->exists($filePath) ? Storage::disk('public')->url($filePath) : null,
                'errors_url' => Storage::disk('public')->exists($errorsFilePath) ? Storage::disk('public')->url($errorsFilePath) : null
            ]);
        }
    }

    // Download endpoint
    public function download(string $file)
    {
        return \Storage::download("exports/{$file}");
    }
}
