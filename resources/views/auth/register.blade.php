@extends('website.auth-layout')

@section('page-title', __('auth.form.register'))

@section('content')
    <div class="pw-auth-heading">
        <p class="pw-auth-kicker">BEGIN YOUR ADVENTURE</p>
        <h2>{{ __('auth.form.register') }}</h2>
        <p>Create your account to join the world of Etherna.</p>
    </div>

    <x-hrace009::validation-errors class="pw-auth-errors" />

    <form method="POST" action="{{ route('register') }}" class="pw-auth-form">
        @csrf

        <x-hrace009::input-box id="name" type="text" name="name" :value="old('name')"
            placeholder="{{ __('auth.form.login') }}" autofocus required autocomplete="name" />

        <x-hrace009::input-box id="email" type="email" name="email" :value="old('email')"
            placeholder="{{ __('auth.form.email') }}" required autocomplete="email" />

        <x-hrace009::input-box id="phonenumber" type="phonenumber" name="phonenumber"
            :value="old('phonenumber')" placeholder="{{ __('auth.form.phonenumber') }}" required
            autocomplete="phonenumber" />

        <x-hrace009::input-box id="password" type="password" name="password"
            placeholder="{{ __('auth.form.password') }}" required autocomplete="new-password" />

        <x-hrace009::input-box id="password_confirmation" type="password" name="password_confirmation"
            placeholder="{{ __('auth.form.confirmPassword') }}" required autocomplete="new-password" />

        @if (!Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
            <x-hrace009::input-box id="pin" type="password" name="pin"
                placeholder="{{ __('auth.form.pin') }}" required autocomplete="new-pin" />
        @endif

        <x-hrace009::input-box id="truename" type="text" name="truename" :value="old('truename')"
            placeholder="{{ __('auth.form.trueName') }}" required autocomplete="truename" />

        @if (config('pw-config.system.apps.captcha'))
            <x-hrace009::captcha />
        @endif

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <x-hrace009::auth.tos-agree />
        @endif

        <x-hrace009::button type="submit" class="btn btn-primary w-full tracking-wide">
            {{ __('auth.form.register') }}
        </x-hrace009::button>
    </form>

    <p class="pw-auth-links">
        {{ __('auth.form.registered') }}
        <a href="{{ route('login') }}">{{ __('auth.form.login') }}</a>
    </p>
@endsection
