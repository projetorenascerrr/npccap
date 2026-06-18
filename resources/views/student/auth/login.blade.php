<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal do Aluno - Entrar | NPCCAP</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-page {
            min-height: 100vh;
            background: radial-gradient(circle at 10% 10%, rgba(16, 185, 129, 0.15), transparent 35%),
                radial-gradient(circle at 90% 90%, rgba(59, 130, 246, 0.15), transparent 35%),
                linear-gradient(160deg, #030712 0%, #081121 50%, #030712 100%);
            color: #e2e8f0;
        }

        .auth-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(16, 185, 129, 0.2);
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.45);
            backdrop-filter: blur(8px);
        }

        .form-control,
        .form-control:focus {
            background: rgba(15, 23, 42, 0.9);
            border-color: rgba(148, 163, 184, 0.2);
            color: #e2e8f0;
        }

        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.2);
        }

        .form-label {
            color: #cbd5e1;
        }

        .text-soft {
            color: #94a3b8;
        }

        .btn-student {
            background: linear-gradient(135deg, #10b981, #059669);
            border: 0;
            transition: all 0.2s;
        }

        .btn-student:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="auth-page d-flex align-items-center">
    <div class="container py-5">
        <div class="auth-card rounded-4 p-4 p-lg-5 mx-auto" style="max-width: 460px;">
            <div class="text-center mb-4">
                <span class="badge text-bg-success-subtle text-success border border-success-subtle mb-2 px-3 py-2">PORTAL DO ALUNO</span>
                <h1 class="h3 fw-semibold mb-1">Acesse sua Área</h1>
                <p class="text-soft">Entre para gerenciar seus cursos e certificados.</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm text-center mb-4">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.perform') }}">
                @csrf

                @if($course_id)
                    <input type="hidden" name="course_id" value="{{ $course_id }}">
                @endif

                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                        class="form-control form-control-lg @error('cpf') is-invalid @enderror" required autofocus>
                    @error('cpf')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Senha</label>
                    <input id="password" type="password" name="password" placeholder="Digite sua senha"
                        class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-student w-100 text-white py-2.5 fw-medium">Entrar</button>
            </form>

            <p class="text-center text-soft small mt-4">
                Não tem cadastro? 
                <a href="{{ route('register', $course_id ? ['course_id' => $course_id] : []) }}" class="text-success text-decoration-none fw-semibold">
                    Cadastre-se aqui
                </a>
            </p>
            <div class="text-center mt-3">
                <a href="{{ route('welcome') }}" class="text-soft small text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Voltar ao início
                </a>
            </div>
        </div>
    </div>
</body>

</html>
