@extends('website.auth-layout')

@section('page-title', __('auth.form.resetPassword'))

@section('content')
    <div class="pw-auth-heading">
        <p class="pw-auth-kicker">ACCOUNT RECOVERY</p>
        <h2>{{ __('auth.form.resetPassword') }}</h2>
        <p>Confirm your password reset to return to your account.</p>
    </div>

    <x-hrace009::validation-errors class="pw-auth-errors" />

    <form method="POST" action="{{ route('password.update') }}" class="pw-auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" id="password" name="password" value="{{ $password }}">
        @if (! Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
            <input type="hidden" id="pin" name="pin" value="{{ $pin }}">
        @endif
        <input type="hidden" id="email" name="email" value="{{ $request->email }}">
        @if (config('pw-config.system.apps.captcha'))
            <x-hrace009::captcha />
        @endif

        <x-hrace009::button type="submit" class="btn btn-primary w-full tracking-wide">
            {{ __('auth.form.resetPassword') }}
        </x-hrace009::button>
    </form>
@endsection
