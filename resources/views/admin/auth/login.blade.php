<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel Administrativo - Entrar | NPCCAP</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-page {
            min-height: 100vh;
            background: radial-gradient(circle at 10% 10%, rgba(239, 68, 68, 0.1), transparent 35%),
                radial-gradient(circle at 90% 90%, rgba(59, 130, 246, 0.12), transparent 35%),
                linear-gradient(160deg, #020617 0%, #0f172a 50%, #020617 100%);
            color: #cbd5e1;
        }

        .auth-card {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(239, 68, 68, 0.25);
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.5);
            backdrop-filter: blur(8px);
        }

        .form-control,
        .form-control:focus {
            background: rgba(15, 23, 42, 0.95);
            border-color: rgba(148, 163, 184, 0.2);
            color: #f8fafc;
        }

        .form-control:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 0.25rem rgba(239, 68, 68, 0.25);
        }

        .form-label {
            color: #94a3b8;
        }

        .btn-admin {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border: 0;
            transition: all 0.2s;
        }

        .btn-admin:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="auth-page d-flex align-items-center">
    <div class="container py-5">
        <div class="auth-card rounded-4 p-4 p-lg-5 mx-auto" style="max-width: 460px;">
            <div class="text-center mb-4">
                <span class="badge text-bg-danger-subtle text-danger border border-danger-subtle mb-2 px-3 py-2">PAINEL ADMINISTRATIVO</span>
                <h1 class="h3 fw-semibold mb-1 text-white">Login de Admin</h1>
                <p class="text-secondary small">Acesso restrito para administradores do sistema.</p>
            </div>

            <form method="POST" action="{{ route('admin.login.perform') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail Corporativo</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="admin@npccap.com"
                        class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Senha de Acesso</label>
                    <input id="password" type="password" name="password" placeholder="Digite sua senha"
                        class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-admin w-100 text-white py-2.5 fw-medium">Entrar no Painel</button>
            </form>

            <div class="text-center mt-4 border-top border-secondary pt-3">
                <a href="{{ route('welcome') }}" class="text-secondary small text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Voltar ao site institucional
                </a>
            </div>
        </div>
    </div>
</body>

</html>
