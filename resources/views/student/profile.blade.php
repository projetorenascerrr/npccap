<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Perfil - Portal do Aluno | NPCCAP</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .portal-page {
            background:
                radial-gradient(circle at 10% 10%, rgba(16, 185, 129, 0.15), transparent 35%),
                radial-gradient(circle at 90% 10%, rgba(59, 130, 246, 0.15), transparent 35%),
                linear-gradient(160deg, #030712 0%, #0b1220 45%, #081225 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }

        .portal-card {
            background: rgba(15, 23, 42, 0.74);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.35);
            backdrop-filter: blur(7px);
        }

        .nav-portal {
            background: rgba(15, 23, 42, 0.85);
            border-bottom: 1px solid rgba(148, 163, 184, 0.18);
            backdrop-filter: blur(10px);
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

<body class="portal-page">

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg nav-portal sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-bold" href="{{ route('student.dashboard') }}">
                <img src="{{ asset('images/logo_sejuc.svg') }}" alt="Logo" class="img-fluid" style="height: 32px;">
                <span class="fs-4 tracking-wider text-success">NPCCAP</span>
                <span class="badge text-bg-success-subtle text-success border border-success-subtle d-none d-sm-inline">Portal do Aluno</span>
            </a>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <span class="text-white d-none d-md-inline small">
                    Olá, <strong class="text-success">{{ explode(' ', trim($studentUser->name))[0] }}</strong>
                </span>

                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-light btn-sm">
                    Painel Geral
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container py-5">
        <div class="portal-card rounded-4 p-4 p-lg-5 mx-auto" style="max-width: 600px;">
            <div class="text-center mb-4">
                <h1 class="h3 fw-semibold mb-1 text-white">Editar Minhas Informações</h1>
                <p class="text-soft">Mantenha seus dados atualizados para a emissão correta de certificados.</p>
            </div>

            <!-- Messages -->
            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm text-center mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm text-center mb-4">
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('student.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nome Completo</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $studentUser->name) }}" placeholder="Seu nome completo"
                        class="form-control @error('name') is-invalid @enderror" required autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input id="cpf" type="text" name="cpf" value="{{ old('cpf', $studentUser->cpf) }}" placeholder="000.000.000-00"
                        class="form-control @error('cpf') is-invalid @enderror" required>
                    @error('cpf')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $studentUser->email) }}" placeholder="seu.email@exemplo.com"
                        class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4 border-secondary">

                <div class="mb-3">
                    <h2 class="h5 text-white mb-2">Alterar Senha</h2>
                    <p class="text-soft small">Deixe estes campos em branco se não desejar alterar sua senha atual.</p>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Nova Senha</label>
                    <input id="password" type="password" name="password" placeholder="Mínimo 8 caracteres"
                        class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repita a nova senha"
                        class="form-control">
                </div>

                <div class="d-flex gap-3">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary w-100 py-2">Cancelar</a>
                    <button type="submit" class="btn btn-student w-100 text-white py-2 fw-medium">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cpfInput = document.getElementById('cpf');
            if (cpfInput) {
                cpfInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    // Remove all non-digit characters
                    value = value.replace(/\D/g, '');

                    // Limit to 11 digits
                    if (value.length > 11) {
                        value = value.slice(0, 11);
                    }

                    // Apply the CPF mask formatting: 000.000.000-00
                    if (value.length > 9) {
                        value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
                    } else if (value.length > 6) {
                        value = value.replace(/^(\d{3})(\d{3})(\d{1,3})$/, '$1.$2.$3');
                    } else if (value.length > 3) {
                        value = value.replace(/^(\d{3})(\d{1,3})$/, '$1.$2');
                    }

                    e.target.value = value;
                });
            }
        });
    </script>
</body>

</html>