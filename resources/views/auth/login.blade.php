@extends('layouts.guest')

@section('title', 'Sign In - APDL ')

@section('content')

    <div class="grid lg:grid-cols-5 md:grid-cols-2 items-center gap-y-4 h-full w-full">
        <div
            class="max-md:order-1 lg:col-span-3 md:h-screen w-full bg-[#000842] md:rounded-tr-xl md:rounded-br-xl lg:p-12 p-8">
            <img src="https://readymadeui.com/signin-image.webp" class="lg:w-2/3 w-full h-full object-contain block mx-auto"
                alt="login-image" />
        </div>
        <div class="lg:col-span-2 w-full p-8 max-w-lg max-md:max-w-lg mx-auto">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <x-auth-session-error class="mb-4" :error="session('error')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="username" :value="__('Payroll No')" class="text-black"/>
                    <x-text-input id="username" class="block mt-1 w-full p-1" type="text" name="payroll_number" :value="old('payroll_number')"
                        required autofocus autocomplete="payroll_number" />
                    <x-input-error :messages="$errors->get('payroll_number')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />

                    <x-text-input id="password" class="block mt-1 w-full p-1" type="password" name="password" required
                        autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            name="remember">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                  
                    <x-primary-button class="ms-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>

    @endsection
