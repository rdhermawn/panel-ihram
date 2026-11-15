@extends('website.auth-layout')

@section('page-title', __('auth.form.forgotPassword'))

@section('content')
    <div class="pw-auth-heading">
        <p class="pw-auth-kicker">ACCOUNT RECOVERY</p>
        <h2>{{ __('auth.form.forgotPassword') }}</h2>
    </div>

    <div class="pw-auth-description">
        <x-hrace009::auth.forgot-password-description />
    </div>

    @if (session('status'))
        <div class="pw-auth-status" role="status" aria-live="polite">
            {{ session('status') }}
        </div>
    @endif

    <x-hrace009::validation-errors class="pw-auth-errors" />

    <form method="POST" action="{{ route('password.email') }}" class="pw-auth-form">
        @csrf
        <x-hrace009::input-box id="email" type="email" name="email" :value="old('email')" required autofocus
            placeholder="{{ __('auth.form.email') }}" />
        @if (config('pw-config.system.apps.captcha'))
            <x-hrace009::captcha />
        @endif
        <x-hrace009::button type="submit" class="btn btn-primary w-full tracking-wide">
            {{ __('auth.form.sendLinkPassword') }}
        </x-hrace009::button>
    </form>
@endsection
