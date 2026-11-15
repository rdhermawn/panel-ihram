@extends('website.auth-layout')

@section('page-title', 'Log in')
@section('story-title', 'Welcome back. Your world is waiting.')
@section('story-text', 'Log in to return to Etherna Perfect World.')

@section('content')
    <div class="pw-auth-heading">
        <h1>Log in</h1>
        <p>Enter your account details.</p>
    </div>

    @if ($errors->any())
        <div class="pw-auth-errors" role="alert">
            <p>Check the highlighted fields.</p>
            @foreach (Illuminate\Support\Arr::except($errors->getMessages(), ['name', 'password', 'pin']) as $messages)
                @foreach ($messages as $message)
                    <p>{{ $message }}</p>
                @endforeach
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="pw-auth-form">
        @csrf

        <div class="pw-auth-field">
            <label for="name">Username</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autofocus autocomplete="username" required
                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" aria-describedby="name-hint{{ $errors->has('name') ? ' name-error' : '' }}">
            <p id="name-hint" class="pw-auth-hint">Letters and numbers only.</p>
            @error('name')
                <p id="name-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="pw-auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" autocomplete="current-password" required
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" @error('password') aria-describedby="password-error" @enderror>
            @error('password')
                <p id="password-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        @if (! Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
        <div class="pw-auth-field">
            <label for="pin">PIN</label>
            <input id="pin" type="password" name="pin" inputmode="numeric" autocomplete="off" required
                aria-invalid="{{ $errors->has('pin') ? 'true' : 'false' }}" @error('pin') aria-describedby="pin-error" @enderror>
            @error('pin')
                <p id="pin-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>
        @endif

        @if (config('pw-config.system.apps.captcha'))
            <x-hrace009::captcha />
        @endif

        <div class="pw-auth-login-options">
            <label class="pw-auth-checkbox" for="remember_me">
                <input type="checkbox" id="remember_me" name="remember">
                <span>{{ __('auth.form.remember') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Reset password</a>
            @endif
        </div>

        <button type="submit">Log in</button>
        <p class="pw-auth-links">No account yet? <a href="{{ route('register') }}">Create one</a></p>
    </form>
@endsection
