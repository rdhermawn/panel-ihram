<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} @yield('title')</title>

    @if( config('pw-config.logo') === 'img/logo/logo.png' )
        <link rel="shortcut icon" href="{{ asset(config('pw-config.logo')) }}"/>
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset(config('pw-config.logo')) }}"/>
    @elseif( ! config('pw-config.logo') )
        <link rel="shortcut icon" href="{{ asset('img/logo/logo.png') }}"/>
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/logo/logo.png') }}"/>
    @else
        <link rel="shortcut icon" href="{{ asset('uploads/logo/' . config('pw-config.logo')) }}"/>
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('uploads/logo/' . config('pw-config.logo')) }}"/>
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

<style>

.glassmorphism{
    background:rgba(0,0,0,.15);
    backdrop-filter:blur(15px);
    border-radius:25px;
    border:1px solid rgba(255,255,255,.1);
    box-shadow:0 8px 32px rgba(255,165,0,.12);
}

.custom-gradient{
    background:linear-gradient(45deg,#06b6d4,#10b981);
    transition:.3s;
}

.custom-gradient:hover{
    background:linear-gradient(45deg,#0891b2,#059669);
}

/* ===========================
   FULL SCREEN + SCROLL
=========================== */

html,
body{
    margin:0;
    padding:0;
    width:100%;
    min-height:100vh;
    overflow-x:hidden;
    overflow-y:auto;
    font-family:'Cairo',sans-serif;
}

/* Background */

.background-image{
    position:fixed;
    inset:0;
    background:url("{{ asset('images/bg.png') }}") center center/cover no-repeat;
    z-index:-1;
}

/* Wrapper */

.page-wrapper{
    width:100%;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:30px;
    box-sizing:border-box;
}

/* Main */

main{
    width:100%;
}

/* Policy */

.policy-container{
    width:100%;
    max-width:1400px;
    margin:auto;
}

/* Markdown */

.policy-container article,
.policy-container .prose,
.policy-container .markdown-body{
    width:100%;
    max-width:none;
    word-break:break-word;
    overflow-wrap:anywhere;
}

/* Mobile */

@media(max-width:768px){

.page-wrapper{
    padding:15px;
}

}

</style>

<script src="{{ mix('js/app.js') }}" defer></script>

</head>

<body class="antialiased">

<div class="background-image"></div>

<div class="page-wrapper">

    <div class="policy-container">

        <x-hrace009::auth.general-frame>

            <x-hrace009::auth.dark-frame>

                <main>

                    <x-hrace009::auth.inside-main>

                        {{ $slot }}

                    </x-hrace009::auth.inside-main>

                </main>

            </x-hrace009::auth.dark-frame>

        </x-hrace009::auth.general-frame>

    </div>

</div>

<x-hrace009::auth.script/>

</body>
</html>