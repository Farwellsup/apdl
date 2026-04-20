@php
    $titleFormKey = $titleFormKey ?? 'title';
    $titleFormLabel = $titleFormLabel ?? 'Title';

    if (hasRole(['Owner', 'Administrator'])) {
        $companyList = app(App\Repositories\CompanyRepository::class)->listAll('title');
    } elseif (hasRole(['Company HR'])) {
        $companyList = app(App\Repositories\CompanyRepository::class)->listAll('title', Auth::user()->company_id);
    }

@endphp

<x-twill::select name="company_id" label="Company" :native="true" :options="$companyList ?? []" placeholder="Select a company"
    :required="true" />

<x-twill::input :name="$titleFormKey" :label="$titleFormKey === 'title' && $titleFormLabel === 'Title' ? twillTrans('twill::lang.modal.title-field') : $titleFormLabel" :translated="$translateTitle ?? false" :required="true" on-change="formatPermalink" />

@if ($permalink ?? true)
    <x-twill::input name="slug" :label="twillTrans('twill::lang.modal.permalink-field')" :translated="true" ref="permalink" :prefix="$permalinkPrefix ?? ''" />
@endif
