@extends('website.auth-layout')

@section('page-title', 'Reset password')

@section('content')
    <div class="pw-auth-heading">
        <h1>Reset password</h1>
        <p>Request a reset link by email.</p>
    </div>

    @if (session('status'))
        <div class="pw-auth-status" role="status" aria-live="polite">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="pw-auth-errors" role="alert">
            <p>Check the highlighted fields.</p>
            @foreach (Illuminate\Support\Arr::except($errors->getMessages(), ['email']) as $messages)
                @foreach ($messages as $message)
                    <p>{{ $message }}</p>
                @endforeach
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="pw-auth-form">
        @csrf
        <div class="pw-auth-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus autocomplete="email" required
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" @error('email') aria-describedby="email-error" @enderror>
            @error('email')
                <p id="email-error" class="pw-auth-field-error">{{ $message }}</p>
            @enderror
        </div>
        @if (config('pw-config.system.apps.captcha'))
            <x-hrace009::captcha />
        @endif
        <button type="submit">Send password reset link</button>
    </form>
    <p class="pw-auth-links"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection
