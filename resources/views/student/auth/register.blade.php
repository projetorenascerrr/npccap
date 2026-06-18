<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal do Aluno - Cadastrar | NPCCAP</title>

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
        <div class="auth-card rounded-4 p-4 p-lg-5 mx-auto" style="max-width: 500px;">
            <div class="text-center mb-4">
                <span class="badge text-bg-success-subtle text-success border border-success-subtle mb-2 px-3 py-2">PORTAL DO ALUNO</span>
                <h1 class="h3 fw-semibold mb-1">Crie sua Conta</h1>
                <p class="text-soft">Cadastre-se para acessar cursos e emitir seus certificados.</p>
            </div>

            <form method="POST" action="{{ route('register.perform') }}">
                @csrf

                @if($course_id)
                    <input type="hidden" name="course_id" value="{{ $course_id }}">
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Nome Completo</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Seu nome completo"
                        class="form-control @error('name') is-invalid @enderror" required autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                        class="form-control @error('cpf') is-invalid @enderror" required>
                    @error('cpf')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="seu.email@exemplo.com"
                        class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="birth_date" class="form-label">Data de Nascimento</label>
                    <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}"
                        class="form-control @error('birth_date') is-invalid @enderror" required>
                    @error('birth_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input id="password" type="password" name="password" placeholder="Mínimo 8 caracteres"
                        class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repita a senha"
                        class="form-control" required>
                </div>

                <button type="submit" class="btn btn-student w-100 text-white py-2 fw-medium">Cadastrar</button>
            </form>

            <p class="text-center text-soft small mt-4">
                Já tem cadastro? 
                <a href="{{ route('login', $course_id ? ['course_id' => $course_id] : []) }}" class="text-success text-decoration-none fw-semibold">
                    Entre aqui
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
