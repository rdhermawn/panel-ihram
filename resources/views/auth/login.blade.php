<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/images/favicon.png" type="image/x-icon">
    <title>Eterna Perfect World</title>

    <!-- Styles -->
    <link rel="stylesheet" href="./assets/css/app.css">

    <!-- Efek zoom in-out background -->
    <style>
        @keyframes zoomBg {
            0% { background-size: 100%; }
            50% { background-size: 110%; }
            100% { background-size: 100%; }
        }

        main {
            animation: zoomBg 18s ease-in-out infinite;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
        }

        /* Tombol biru neon */
        .btn-primary {
            background-color: #00bfff;
            box-shadow: 0 0 15px #00bfff, 0 0 30px #00bfff inset;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1ec8ff;
            box-shadow: 0 0 25px #00bfff, 0 0 45px #00bfff inset;
        }

        /* Tombol outline */
        .btn-outline {
            border: 1px solid #00bfff;
            color: #00bfff;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: #00bfff;
            color: #000;
            box-shadow: 0 0 20px #00bfff;
        }

        /* Input field */
        input {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid #00bfff;
            color: #fff;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        input:focus {
            box-shadow: 0 0 15px #00bfff;
            border-color: #1ec8ff;
        }

        /* Divider */
        .divider {
            color: #00bfff;
            text-align: center;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #00bfff;
        }

        .divider::before { left: 0; }
        .divider::after { right: 0; }

        /* Judul Log In futuristik */
        .login-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            text-transform: uppercase;
            background: linear-gradient(90deg, #00bfff, #1ec8ff, #00ffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 15px #00bfff, 0 0 25px #00ffff;
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
            animation: glowPulse 2s infinite alternate;
        }

        @keyframes glowPulse {
            from {
                text-shadow: 0 0 10px #00bfff, 0 0 20px #00ffff;
            }
            to {
                text-shadow: 0 0 25px #00ffff, 0 0 35px #00bfff;
            }
        }
    </style>

    <!-- Font Awesome Icon -->
    <script src="https://kit.fontawesome.com/d72aa02c52.js" crossorigin="anonymous"></script>
</head>

<body>
    <main class="text-white bg-[url(../images/banner.png)] bg-cover bg-center relative">
        <div class="xl:container xl:items-center lg:px-0 flex items-start justify-center min-h-screen px-6 mx-auto">
            <div class="lg:w-1/3 lg:my-10 lg:px-0 z-10 w-full my-20">
                <link rel="stylesheet" href="{{ asset('assets/css/register.css') }}">

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Judul Log In Futuristik -->
                    <p class="login-title">LOG IN</p>

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

                    <div class="flex items-center justify-between">
                        <x-hrace009::auth.remember-me />
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm text-blue-400 hover:underline">{{ __('auth.form.forgotPassword') }}</a>
                        @endif
                    </div>

                    <div class="flex justify-center">
                        <x-hrace009::button type="submit" class="btn btn-primary w-full tracking-wide">
                            {{ __('auth.form.login') }}
                        </x-hrace009::button>
                    </div>

                    <div class="divider my-5">OR</div>

                    <div class="flex justify-center">
                        <a href="/register" class="btn btn-outline w-full">Create an Account</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="./assets/js/app.js" defer></script>
</body>

</html>
