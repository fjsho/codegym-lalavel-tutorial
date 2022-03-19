<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/errors-illustrated-layout.css') }}">

    </head>
    <body class="antialiased font-sans">
        <div class="md:flex min-h-screen">
            <div class="w-full ml-6 md:w-1/2 bg-transparent flex items-center justify-start">
                <div class="max-w-sm m-8">
                    <div class="w-full flex flex-col">
                        <div class="text-black text-5xl md:text-15xl font-black">
                            @yield('code', __('Oh no'))
                        </div>
                        <p class="text-grey-darkest text-2xl md:text-3xl font-light leading-normal">
                            @yield('heading')
                        </p>
                        <p class="h-12 text-grey-darkest text-base md:text-base font-light mb-4 leading-normal">
                            @yield('message')
                        </p>
                    </div>
                    <a href="{{ app('router')->has('dashboard') ? route('dashboard') : url('/') }}">
                        <button class="bg-white text-grey-darkest font-bold uppercase tracking-wide py-3 px-6 border-2 border-grey-light hover:border-grey rounded-lg">
                            {{ __('Back to page :page', ['page' =>__('Dashboard')]) }}
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
