@auth


@if ($needsCompletion)
  <div
            x-data
            x-init="$nextTick(() => $dispatch('open-modal', 'profile-completion'))"
        ></div>

        <x-modal name="profile-completion" :show="false" maxWidth="lg" :closeable="false">

            <div class="bg-primary px-6 vp-4">
                <div class="flex items-center gap-3 p-3">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-white/10">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-white leading-tight">
                            Complete Your Profile
                        </h2>
                        <p class="text-sm text-white mt-0.5">
                            Please fill in the missing details {{(in_array('reset_pd', $missingFields))? 'and change your password to a more personalized one' : '' }}  to continue.

                        </p>
                    </div>
                </div>
            </div>

            <form
                method="POST"
                action="{{route('profile.update')}}"
                class="bg-white"
                x-data="{ loading: false }"
                x-on:submit="loading = true"
            >
                @csrf

                <div class="px-6 py-6 space-y-5">

                    @if (session('profile_error'))
                        <div class="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                            {{ session('profile_error') }}
                        </div>
                    @endif 

                       
                    @if (in_array('department_id', $missingFields))
                        <div>
                            <label for="department_id-btn" class="block text-sm font-medium text-gray-700 mb-1">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <x-select
                                id="department_id"
                                name="department_id"
                                placeholder="Select Department"
                                :options="$departments->pluck('title', 'id')->toArray()"
                                :selected="old('department_id')"
                                required
                            />
                            @error('department_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif


                        @if (in_array('unit_id', $missingFields))
                        <div>
                            <label for="unit_id-btn" class="block text-sm font-medium text-gray-700 mb-1">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <x-select
                                id="unit_id"
                                name="unit_id"
                                placeholder="Select Unit"
                                :options="$units->pluck('title', 'id')->toArray()"
                                :selected="old('unit_id')"
                                required
                            />
                            @error('unit_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                        @if (in_array('country_id', $missingFields))
                        <div>
                            <label for="country_id-btn" class="block text-sm font-medium text-gray-700 mb-1">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <x-select
                                id="country_id"
                                name="country_id"
                                placeholder="Select Country"
                                :options="$countries->pluck('title', 'id')->toArray()"
                                :selected="old('country_id')"
                                required
                            />
                            @error('country_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if (in_array('gender_id', $missingFields))
                        <div>
                            <label for="gender_id-btn" class="block text-sm font-medium text-gray-700 mb-1">
                                Gender <span class="text-red-500">*</span>
                            </label>
                            <x-select
                                id="gender_id"
                                name="gender_id"
                                placeholder="I am a..."
                                :options="$genders->pluck('title', 'id')->toArray()"
                                :selected="old('gender_id')"
                                required
                            />
                            @error('gender_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    
                    @if (in_array('reset_pd', $missingFields))
                            
                       <div>
                            <label for="old_password-btn" class="block text-sm font-medium text-gray-700 mb-1">
                                Old Password <span class="text-red-500">*</span>
                            </label>
                            <x-input
                             id="old_password"
                             name="old_password"
                             placeholder="Old password"
                             type="password"
                             required
                            />

                             @error('old_password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        
                       <div>
                            <label for="password-btn" class="block text-sm font-medium text-gray-700 mb-1">
                               Password <small>(The password should be
                        minimum 8 characters long, contain uppercase, lowercase, numbers and symbols
                        (*,&,$,#,_))</small> <span class="text-red-500">*</span>
                            </label>
                             <x-input
                             id="password"
                             name="password"
                             placeholder="New password"
                             minlength="8"
                             type="password"
                             required
                            />

                             @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                         <div>
                            <label for="password_confirmation-btn" class="block text-sm font-medium text-gray-700 mb-1">
                               Confirm Password <span class="text-red-500">*</span>
                            </label>

                             <x-input
                             id="password_confirmation"
                             name="password_confirmation"
                             placeholder="Confirm password"
                             minlength="8"
                             type="password"
                             required
                            />

                             @error('password_confirmation')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                    @endif

                    
                </div>

                <div class="px-6 vp-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3 py-4">
                    <p class="flex items-start text-xs text-gray-500">
                        <input 
                            type="checkbox"
                            name="policy_agree"
                            id="policy_agree"
                            value="1"
                            required
                            class="w-5 h-5 mt-0.5 border-2 border-gray-300 rounded focus:ring-0 focus:ring-offset-0 text-black flex-shrink-0"
                        >
                        <span class="ml-3">
                            I agree to APD's <a href="/pages/privacy-policy" class="underline font-semibold text-black hover:text-gray-600">Privacy Policy</a> and <a href="/pages/terms-conditions" class="underline font-semibold text-black hover:text-gray-600">Terms of Use</a>
                        </span>
                        @error('policy_agree')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </p>
                    <button
                        type="submit"
                        x-bind:disabled="loading"
                        class="inline-flex items-center gap-2 rounded-md primary-button px-5 py-2.5 text-sm font-semibold text-white shadow-sm
                               disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-150 mt-2"
                    >
                        <svg x-show="loading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="loading ? 'Saving…' : 'Save & Continue'"></span>
                    </button>
                </div>
            </form>

        </x-modal>

        @if ($errors->any())
            <div x-data x-init="$nextTick(() => $dispatch('open-modal', 'profile-completion'))"></div>
        @endif



@endif


@endauth