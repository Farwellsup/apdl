@php
    $titleFormKey = $titleFormKey ?? 'title';
    $titleFormLabel = $titleFormLabel ?? 'Title';

    if (hasRole(['Owner', 'Administrator'])) {
        $companyList = app(App\Repositories\CompanyRepository::class)->listAll('title');
    } elseif (hasRole(['Company HR'])) {
        $companyList = app(App\Repositories\CompanyRepository::class)->listAll('title', Auth::user()->company_id);
    }

@endphp

@push('extra_css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/twill/multiselect_css/style.css') }}">
    <style>
        .input {
            margin-top: 35px;
            position: relative;
        }

        .input__label {
            display: block;
            color: #262626;
            margin-bottom: 10px;
            word-wrap: break-word;
            position: relative;
        }

        .select__input select {
            font-size: 15px;
            line-height: 33px;
            height: 35px;
            padding: 0 35px 0 14px;
            border-radius: 2px;
            color: #8c8c8c;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            -webkit-padding-end: 35px !important;
            -webkit-padding-start: 14px !important;
            margin-top: -1px;
            width: 100%;
            margin: 0;
            outline: none;
            height: 45px;
        }

        .select__input--large select {
            line-height: 43px;
        }

        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 45px;
            user-select: none;
            -webkit-user-select: none;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: none;
            border-radius: unset;
        }
    </style>
@endpush

<x-twill::select name="company_id" label="Company" :native="true" :options="$companyList ?? []" placeholder="Select a company"
    :required="true" multiple />


<x-twill::multi-select
    name="department_id"
    label="Department"
    :options="[]"
/>

{{-- @formField('multi_select', [
    'name' => 'department_id',
    'label' => 'Department',
    'options' => [],
]) --}}


<x-twill::input :name="$titleFormKey" :label="$titleFormKey === 'title' && $titleFormLabel === 'Title' ? twillTrans('twill::lang.modal.title-field') : $titleFormLabel" :translated="$translateTitle ?? false" :required="true" on-change="formatPermalink" />

@if ($permalink ?? true)
    <x-twill::input name="slug" :label="twillTrans('twill::lang.modal.permalink-field')" :translated="true" ref="permalink" :prefix="$permalinkPrefix ?? ''" />
@endif


@push('extra_js')


    <script>
        function findFieldByName(vm, name) {
            if (!vm) return null;

            // check current component
            if (vm.name === name) return vm;

            // search children recursively
            for (let child of vm.$children) {
                const found = findFieldByName(child, name);
                if (found) return found;
            }

            return null;
        }

        document.addEventListener('change', async function(e) {

            if (e.target.name === 'company_id') {

                const categoryId = e.target.value;

                if (!categoryId) return;

                try {
                    const res = await fetch(`/admin/getDepartments/${categoryId}`);
                    const data = await res.json();

                    const field = findFieldByName(window.vm, 'department_id');

                    if (!field) {
                        console.warn('Multiselect not found');
                        return;
                    }

                    // update options
                    field.options = data.map(item => ({
                        value: item.id,
                        label: item.title
                    }));

                    // reset selected values
                    field.value = [];

                } catch (e) {
                    console.error(e);
                }
            }

        });
    </script>
@endpush
