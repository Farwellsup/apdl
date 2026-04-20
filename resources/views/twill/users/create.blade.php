@php
    $isSuperAdmin = isset(Auth::user()->role) ? Auth::user()->role->name === 'SUPERADMIN' : false;

    if (hasRole(['Owner', 'Administrator'])) {
        $companyList = app(App\Repositories\CompanyRepository::class)->listAll('title');
    } elseif (hasRole(['Company HR'])) {
        $companyList = app(App\Repositories\CompanyRepository::class)->listAll('title', Auth::user()->company_id);
    }

@endphp

@push('extra_css')
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

<x-twill::select name="company_id" label="Company" :native="true" :options="$companyList ?? []" placeholder="Select a company"
    :required="true" />

<div class="locale">
    <div class="locale__item">
        <div class="input input-wrapper-user_list" data-lang="en" fieldname="user_list" initialvalue="">
            <label for="user_list" class="input__label"> User File<!----><!----><!----></label><!---->
            <div class="fileField"><!----><!---->
                <div class="fileField__trigger">
                    <input type="file" name="user_list[en]" id="fileInput" style="display:none">
                    <button data-v-59eeac35="" type="button" class="button button--ghost"
                        onclick="document.getElementById('fileInput').click()">Upload file</button>
                    <span class="fileField__note f--small">Add excel sheet with users details.</span>
                </div>
            </div><!----><!---->
        </div>
    </div>
</div>


@push('extra_js')
    <script>
        document.addEventListener('click', () => {
            setTimeout(() => {
                const modal = document.querySelector('.modal');
                if (modal) {
                    const form = modal.querySelector('form');
            if (form) {
                form.enctype = 'multipart/form-data';
            }
                }
            }, 150);
        });
    </script>
@endpush
