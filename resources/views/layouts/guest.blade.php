<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts._partials._header')

<body class="font-sans text-black antialiased">
    @include('layouts._partials._navigation')
    <div class="min-h-screen flex flex-col sm:justify-center items-center w-full  ">


      @yield('content')

    @include('layouts._partials._components')
    </div>
</body>

</html>
