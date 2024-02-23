<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ getSettings('app_name') }} | {{ $title }}</title>

    <link href="{{ asset('assets/admin/vendor/remix-icon/remixicon.css') }}" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ asset('frontend/css/main.css') }}"> --}}

    {{-- Library AOS --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    {{-- Script JQUERY --}}
    <script src="{{ asset('assets/admin/vendor/jquery/jquery.min.js') }}"></script>
    
    {{-- Init Owlcaraousel --}}
    <script src="{{ asset('assets/frontend/vendor/owlcaraousel/owl.carousel.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendor/owlcaraousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendor/owlcaraousel/owl.theme.default.min.css') }}">


    {{-- CDN FLOWBEET --}}
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" /> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>

    <style>
        * {
          font-family: 'Poppins', sans-serif;
        }
        body {
            scroll-behavior: smooth;
        }
      </style>
    @vite('resources/css/app.css')


    @if (config('sweetalert.animation.enable'))
        <link rel="stylesheet" href="{{ config('sweetalert.animatecss') }}">
    @endif

    @if (config('sweetalert.theme') != 'default')
        <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-{{ config('sweetalert.theme') }}" rel="stylesheet">
    @endif

    @if (config('sweetalert.alwaysLoadJS') === false && config('sweetalert.neverLoadJS') === false)
        <script src="{{ $cdn ?? asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    @endif

    @include('frontend.layouts.scripts.script-socket')
</head>
<body class="bg-slate-100">

    @php
        if(Auth::check()) {
            $user = Auth::user();
            $totalCart = App\Models\Cart::where('uuid_user', $user->uuid)->count();
            $totalChat = 0;
        }else {
            $totalCart = null;
            $totalChat = null;
        }
    @endphp

    @include('sweetalert::alert')

    @include('frontend.components.header.navbar', ['totalChat' => $totalChat, 'totalCart' => $totalCart])

    @yield('content')

    <x-frontend.layouts.footer />    
    <script>
        AOS.init();
    </script>

    <script>
        $.fn.check = function(callback = null) {
            if(callback) {
                callback(this);
            }else {
                $(this).prop('checked', true);
            }
            return this;
        }

        $.fn.unCheck = function() {
            $(this).prop('checked', false);
            return this;
        }

        $.fn.checkedAll = function(className) {
            const checkboxes = $('.'+className);
            $.each(checkboxes, function(index, value) {
                $(value).prop('checked', true);
            });

            return this;
        }

        $.fn.unCheckedAll = function(className) {
            const checkboxes = $('.'+className);
            $.each(checkboxes, function(index, value) {
                $(value).prop('checked', false);
            });

            return this;
        }

        $.fn.rotate = function(rotate = 1) {
            if(rotate == 0) {
                $(this).removeClass('rotate-180').addClass('rotate-0');
            }else {
                if(!$(this).hasClass('rotate-180')) {
                    $(this).addClass('rotate-180').removeClass('rotate-0');
                    
                    return this;
                }
                
                $(this).removeClass('rotate-180').addClass('rotate-0');
            }
            return this;
        }
    </script>
</body>
</html>