@extends('website.auth-layout')

@section('page-title', __('auth.form.login'))

@section('content')
    <div class="pw-auth-heading">
        <p class="pw-auth-kicker">ACCOUNT PORTAL</p>
        <h2>{{ __('auth.form.login') }}</h2>
        <p>Sign in to continue your journey.</p>
    </div>

    <x-hrace009::validation-errors class="pw-auth-errors" />

    <form method="POST" action="{{ route('login') }}" class="pw-auth-form">
        @csrf

        <x-hrace009::input-box id="name" type="text" name="name" :value="old('name')"
            placeholder="{{ __('auth.form.login') }}" autofocus required />

        <x-hrace009::input-box id="password" type="password" name="password" required
            autocomplete="current-password" placeholder="{{ __('auth.form.password') }}" />

        @if (! Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
            <x-hrace009::input-box id="pin" type="password" name="pin" required autocomplete="current-pin"
                placeholder="{{ __('auth.form.pin') }}" />
        @endif

        @if (config('pw-config.system.apps.captcha'))
            <x-hrace009::captcha />
        @endif

        <div class="pw-auth-login-options">
            <x-hrace009::auth.remember-me />
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">{{ __('auth.form.forgotPassword') }}</a>
            @endif
        </div>

        <x-hrace009::button type="submit" class="btn btn-primary w-full tracking-wide">
            {{ __('auth.form.login') }}
        </x-hrace009::button>

        <div class="divider">OR</div>
        <a href="{{ route('register') }}" class="btn btn-outline">Create an Account</a>
    </form>
@endsection
