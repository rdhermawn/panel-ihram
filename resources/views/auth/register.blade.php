@extends('website.auth-layout')

@section('page-title', 'Register')
@section('auth-width', 'pw-auth-main-register')

@section('content')
    <div class="pw-auth-heading">
        <h1>Register</h1>
        <p>Create your Etherna account.</p>
    </div>

    @if ($errors->any())
        <div class="pw-auth-errors" role="alert">
            <p>Check the highlighted fields.</p>
            @foreach (Illuminate\Support\Arr::except($errors->getMessages(), ['name', 'email', 'phonenumber', 'password', 'password_confirmation', 'pin', 'truename', 'terms']) as $messages)
                @foreach ($messages as $message)
                    <p>{{ $message }}</p>
                @endforeach
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="pw-auth-form">
        @csrf

        <div class="pw-auth-field">
            <label for="name">Username</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autofocus autocomplete="username" required
                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @error('name') aria-describedby="name-error" @enderror>
            @error('name')
                <p id="name-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="pw-auth-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" @error('email') aria-describedby="email-error" @enderror>
            @error('email')
                <p id="email-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="pw-auth-field">
            <label for="phonenumber">Phone number</label>
            <input id="phonenumber" type="tel" name="phonenumber" value="{{ old('phonenumber') }}" inputmode="tel" autocomplete="tel" required
                aria-invalid="{{ $errors->has('phonenumber') ? 'true' : 'false' }}" @error('phonenumber') aria-describedby="phonenumber-error" @enderror>
            @error('phonenumber')
                <p id="phonenumber-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="pw-auth-passwords">
        <div class="pw-auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" autocomplete="new-password" required
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" @error('password') aria-describedby="password-error" @enderror>
            @error('password')
                <p id="password-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="pw-auth-field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required
                aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}" @error('password_confirmation') aria-describedby="password_confirmation-error" @enderror>
            @error('password_confirmation')
                <p id="password_confirmation-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>
        </div>

        @if (!Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
        <div class="pw-auth-field">
            <label for="pin">PIN</label>
            <input id="pin" type="password" name="pin" inputmode="numeric" autocomplete="off" required
                aria-invalid="{{ $errors->has('pin') ? 'true' : 'false' }}" @error('pin') aria-describedby="pin-error" @enderror>
            @error('pin')
                <p id="pin-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>
        @endif

        <div class="pw-auth-field">
            <label for="truename">Full name</label>
            <input id="truename" type="text" name="truename" value="{{ old('truename') }}" autocomplete="name" required
                aria-invalid="{{ $errors->has('truename') ? 'true' : 'false' }}" @error('truename') aria-describedby="truename-error" @enderror>
            @error('truename')
                <p id="truename-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>

        @if (config('pw-config.system.apps.captcha'))
            <x-hrace009::captcha />
        @endif

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="pw-auth-terms">
                <label class="pw-auth-checkbox" for="terms">
                    <input type="checkbox" id="terms" name="terms"
                        aria-invalid="{{ $errors->has('terms') ? 'true' : 'false' }}" @error('terms') aria-describedby="terms-error" @enderror>
                    <span>{!! __('I agree to the :terms_of_service and :privacy_policy', [
                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'">'.__('Terms of Service').'</a>',
                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'">'.__('Privacy Policy').'</a>',
                    ]) !!}</span>
                </label>
                @error('terms')
                    <p id="terms-error" class="pw-auth-field-error">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <button type="submit">Register</button>
    </form>

    <p class="pw-auth-links">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
@endsection
