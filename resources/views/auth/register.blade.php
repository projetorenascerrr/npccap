<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar | NPCCAP</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-page {
            min-height: 100vh;
            background: radial-gradient(circle at 10% 10%, rgba(22, 163, 74, 0.18), transparent 35%),
                radial-gradient(circle at 90% 0%, rgba(14, 165, 233, 0.2), transparent 33%),
                linear-gradient(160deg, #030712 0%, #0b1220 45%, #081225 100%);
            color: #e2e8f0;
        }

        .auth-card {
            background: rgba(15, 23, 42, 0.86);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.35);
            backdrop-filter: blur(7px);
        }

        .form-control,
        .form-control:focus {
            background: rgba(15, 23, 42, 0.96);
            border-color: rgba(148, 163, 184, 0.24);
            color: #e2e8f0;
        }

        .form-label {
            color: #cbd5e1;
        }

        .text-soft {
            color: #94a3b8;
        }
    </style>
</head>

<body class="auth-page d-flex align-items-center">
    <div class="container py-5">
        <div class="auth-card rounded-4 p-4 p-lg-5 mx-auto" style="max-width: 460px;">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo_sejuc.svg') }}" alt="Logo" class="img-fluid" style="height: 32px;">
            </div>
            <h1 class="h3 fw-semibold mb-3 text-center">Crie sua conta</h1>
            <p class="text-soft text-center mb-4">Cadastre-se para acessar a plataforma de inscrição.</p>

            <form method="POST" action="{{ route('register.perform') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        class="form-control form-control-lg @error('name') is-invalid @enderror" required autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="form-control form-control-lg @error('email') is-invalid @enderror" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input id="password" type="password" name="password"
                        class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirmar senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="form-control form-control-lg" required>
                </div>

                <button type="submit" class="btn btn-brand w-100 text-white">Cadastrar</button>
            </form>

            <p class="text-center text-soft small mt-4">Já tem conta? <a href="{{ route('login') }}"
                    class="text-decoration-none">Entre aqui</a>.</p>
        </div>
    </div>
</body>

</html>
