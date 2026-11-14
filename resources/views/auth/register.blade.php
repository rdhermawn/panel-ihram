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

        /* Efek neon pada input */
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

        /* Judul Register futuristik */
        .register-title {
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

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Judul Futuristik -->
                    <p class="register-title">REGISTER</p>

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

                    <div class="flex items-center justify-between">
                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <x-hrace009::auth.tos-agree />
                        @endif
                    </div>

                    <x-hrace009::button type="submit" class="btn btn-primary w-full tracking-wide">
                        {{ __('auth.form.register') }}
                    </x-hrace009::button>
                </form>

                <div class="text-sm text-gray-400 dark:text-gray-400 text-center mt-4">
                    {{ __('auth.form.registered') }}
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                        {{ __('auth.form.login') }}
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="./assets/js/app.js" defer></script>
</body>

</html>
