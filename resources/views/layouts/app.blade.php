<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

   @include('layouts._partials._header')
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 ">
            @include('layouts._partials._navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white  shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}


                 @include('layouts._partials._components')
            </main>
        </div>
    </body>
</html>
