@extends('twill::layouts.free')
@push('extra_css')
    <style>
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
        }

        .select__input select {
            padding-right: 2em;
            background: none;
            border: 1px solid transparent;
            appearance: none;
            -webkit-appearance: none;
        }

        .select__input select {
            width: 100%;
            margin: 0;
            outline: none;
            padding: .6em .8em .5em .8em;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            font-size: 16px;
        }

        .select__input--large select {
            line-height: 20px;
        }

        .select__input--large select,
        .select__input--large {
            height: 45px;
        }
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

        .fileField {
            display: block;
            border-radius: 2px;
            border: 1px solid #e5e5e5;
            overflow-x: hidden;
        }

        .fileField__trigger {
            padding: 10px;
            position: relative;
            border-top: 1px solid #f2f2f2;
        }

        .button {
            background-color: transparent;
            -webkit-appearance: none;
            cursor: pointer;
            font-size: 1em;
            outline: none;
            margin: 0;
            border: 0 none;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            letter-spacing: inherit;
            display: inline-block;
            border-radius: 2px;
            padding: 0 30px;
            height: 40px;
            line-height: 38px;
            text-align: center;
            transition: color .2s linear, border-color .2s linear, background-color .2s linear;
            text-decoration: none;
        }

        .button--ghost {
            height: 35px;
            line-height: 33px;
            border-radius: 17.5px;
            background-color: transparent;
            border: 1px solid #d9d9d9;
            color: #8c8c8c;
            padding: 0 20px;
        }

        .button--ghost:hover {
            border-color: #262626;
            color: #262626;
        }

        .f--small {
            font-size: 13px;
        }

        .fileField__note {
            color: #8c8c8c;
            float: right;
            position: absolute;
            bottom: 18px;
            right: 15px;
            display: none;
        }

        @media screen and (min-width: 600px) {
            .fileField__note {
                display: inline-block;
            }
        }
    </style>
@endpush

@section('customPageContent')
    <form class="form-horizontal form-create" method="post" action="{{ route('twill.uploadUsers') }}"
        enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="card-header">
            <h1> Upload Accounts </h1>
        </div>

        <div >
            <div   class="input input-wrapper-company_id">
                <label  for="company_id-1776678186037" class="input__label"> Company
                    <span  class="input__required">*</span><!----><!----></label><!---->
                <span   class="select__input select__input--large">

                    <select name="company_id" class="form-control">
                        <option value="">Select Company</option>
                        @foreach ($companyList as $key => $company)
                            <option value="{{ $key }}">{{ $company }}</option>
                        @endforeach

                    </select>
                </span><!----><!---->
            </div><!---->
        </div>

      
        <div class="locale">
            <div class="locale__item">
                <div data-lang="en" fieldname="user_list" initialvalue="" class="input input-wrapper-user_list">
                    <label for="user_list" class="input__label"> User File</label>
                    <div class="fileField">
                        <div class="fileField__trigger">
                            <input type="file" name="user_list" id="fileInput" style="display: none;">
                            <button data-v-59eeac35="" type="button" onclick="document.getElementById('fileInput').click()"
                                class="button button--ghost">Upload file</button>
                            <span class="fileField__note f--small">Add excel sheet with users details.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


     
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Submit
            </button>
        </div>

    </form>


@stop
