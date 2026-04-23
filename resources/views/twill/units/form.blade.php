@extends('layouts.admin.custom_form')
@php
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
@section('contentFields')
<input type="hidden" name="unit_id" value="{{ $item->id }}" />
<x-twill::select name="company_id" label="Company" :native="true" :options="$companyList ?? []" placeholder="Select a company"
    :required="true" multiple />

    @formField('multi_select', [
    'name' => 'department_id',
    'label' => 'Department',
    'options' => []
])



@endsection

@push('extra_js')
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/twill/multiselect_js/popper.js') }}"></script>
    <script src="{{ asset('assets/twill/multiselect_js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="{{ asset('assets/twill/multiselect_js/main.js') }}"></script>

    
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

document.addEventListener('change', async function (e) {

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



        // document.addEventListener('change', async function(e) {

        //     if (e.target && e.target.name === 'company_id') {

        //         const categoryId = e.target.value;

        //         console.log(categoryId);

                

        //         const subcategorySelect = document.querySelector('select[name="sectors"]');

        //         console.log(subcategorySelect);

        //         if (!subcategorySelect) return;

        //         subcategorySelect.innerHTML = '<option>Loading...</option>';

        //         if (!categoryId) {

        //             subcategorySelect.innerHTML = '<option value="">Select Department</option>';
        //             return;
        //         }

        //         try {
        //             const res = await fetch(`/admin/getDepartments/${categoryId}`);
        //             const data = await res.json();

        //             let options = '<option value="">Select Department</option>';

        //             data.forEach(item => {
        //                 options += `<option value="${item.id}">${item.title}</option>`;
        //             });

        //             subcategorySelect.innerHTML = options;

        //         } catch (e) {
        //             console.error(e);
        //         }
        //     }

        // });
    </script>
@endpush
