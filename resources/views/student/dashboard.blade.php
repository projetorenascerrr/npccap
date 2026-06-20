<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal do Aluno | NPCCAP</title>

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

        .brand-logo {
            width: 40px;
            height: 40px;
        }

        .text-soft {
            color: #94a3b8;
        }

        .badge-status {
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            padding: 0.35em 0.75em;
        }

        .badge-inscrito {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.4);
        }

        .badge-confirmado {
            background: rgba(245, 158, 11, 0.2);
            color: #fde047;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .badge-certificado {
            background: rgba(16, 185, 129, 0.2);
            color: #a7f3d0;
            border: 1px solid rgba(52, 211, 153, 0.4);
        }

        .badge-pre-inscrito {
            background: rgba(107, 114, 128, 0.2);
            color: #d1d5db;
            border: 1px solid rgba(107, 114, 128, 0.4);
        }

        .course-row-item {
            transition: background-color 0.2s;
        }

        .course-row-item:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        .btn-certificate {
            background: linear-gradient(135deg, #10b981, #059669);
            border: 0;
            transition: all 0.2s;
        }

        .btn-certificate:hover {
            transform: translateY(-1px);
            opacity: 0.95;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>

<body class="portal-page">

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg nav-portal sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-bold" href="#">
                <img src="{{ asset('images/logo_sejuc.svg') }}" alt="Logo" class="img-fluid" style="height: 32px;">
                <span class="fs-4 tracking-wider text-success">NPCCAP</span>
                <span class="badge text-bg-success-subtle text-success border border-success-subtle d-none d-sm-inline">Portal do Aluno</span>
            </a>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <span class="text-white d-none d-md-inline small">
                    Olá, <strong class="text-success">{{ explode(' ', trim($studentUser->name))[0] }}</strong>
                </span>

                <a href="{{ route('welcome') }}" class="btn btn-outline-light btn-sm">
                    Ver Cursos
                </a>

                <a href="{{ route('student.profile.edit') }}" class="btn btn-outline-success btn-sm">
                    Editar Perfil
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

        @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm text-center mb-4">
            {{ session('info') }}
        </div>
        @endif

        <!-- Welcome Banner & Quick Stats -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-lg-8">
                <div class="portal-card rounded-4 p-4 p-lg-5 h-100 d-flex flex-column justify-content-center">
                    <h2 class="display-6 fw-semibold text-white mb-2">Bem-vindo ao seu painel!</h2>
                    <p class="text-soft fs-5 mb-0">Aqui você pode acompanhar suas turmas, verificar suas notas/frequência e baixar seus certificados de capacitação profissional.</p>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="row g-3 h-100">
                    <div class="col-6 col-lg-12">
                        <div class="portal-card rounded-4 p-4 text-center d-flex flex-column justify-content-center h-100">
                            <p class="text-soft mb-1 small uppercase tracking-wider">Cursos Inscritos</p>
                            <span class="display-5 fw-bold text-success">{{ $enrollments->count() }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12">
                        <div class="portal-card rounded-4 p-4 text-center d-flex flex-column justify-content-center h-100">
                            <p class="text-soft mb-1 small uppercase tracking-wider">Certificados Disponíveis</p>
                            <span class="display-5 fw-bold text-success">
                                {{ $enrollments->filter(fn($e) => $e->status === \App\Models\Student::STATUS_CERTIFICADO || $e->certificates->isNotEmpty())->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Courses Section -->
        <section>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h4 fw-semibold text-white mb-0">Meus Cursos e Capacitações</h3>
            </div>

            @if($enrollments->isEmpty())
            <div class="portal-card rounded-4 p-5 text-center">
                <i class="bi bi-journal-bookmark-fill text-soft display-3 mb-3 d-block"></i>
                <h4 class="h5 mb-2 text-white">Você não está inscrito em nenhum curso no momento</h4>
                <p class="text-soft mb-4">Escolha uma de nossas capacitações ativas na página inicial.</p>
                <a href="{{ route('welcome') }}" class="btn btn-success">Ver Cursos Disponíveis</a>
            </div>
            @else
            <div class="portal-card rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr class="border-secondary">
                                <th class="px-4 py-3 bg-transparent text-soft font-semibold">Curso</th>
                                <th class="py-3 bg-transparent text-soft font-semibold text-center">Carga Horária</th>
                                <th class="py-3 bg-transparent text-soft font-semibold text-center">Frequência</th>
                                <th class="py-3 bg-transparent text-soft font-semibold text-center">Nota</th>
                                <th class="py-3 bg-transparent text-soft font-semibold text-center">Status</th>
                                <th class="px-4 py-3 bg-transparent text-soft font-semibold text-end">Certificado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                            <tr class="course-row-item border-secondary">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        @if($enrollment->course->image_path)
                                        <img src="{{ asset('storage/' . $enrollment->course->image_path) }}" alt="" class="rounded me-3" style="width: 50px; height: 35px; object-fit: cover;">
                                        @else
                                        <div class="rounded me-3 bg-secondary d-flex align-items-center justify-content-center" style="width: 50px; height: 35px;">
                                            <i class="bi bi-mortarboard text-white"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <span class="d-block fw-semibold text-white">{{ $enrollment->course->name }}</span>
                                            <span class="text-soft small">Início: {{ $enrollment->course->start_date ? $enrollment->course->start_date->format('d/m/Y') : 'A definir' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center text-white">
                                    {{ $enrollment->course->hours ?: '-' }}h
                                </td>
                                <td class="py-3 text-center text-white">
                                    {{ $enrollment->frequency !== null ? number_format($enrollment->frequency, 1) . '%' : '-' }}
                                </td>
                                <td class="py-3 text-center text-white">
                                    {{ $enrollment->grade !== null ? number_format($enrollment->grade, 1) : '-' }}
                                </td>
                                <td class="py-3 text-center">
                                    @if($enrollment->status === \App\Models\Student::STATUS_INSCRITO)
                                    <span class="badge rounded-pill badge-status badge-inscrito">INSCRITO</span>
                                    @elseif($enrollment->status === \App\Models\Student::STATUS_CONFIRMADO)
                                    <span class="badge rounded-pill badge-status badge-confirmado">CONFIRMADO</span>
                                    @elseif($enrollment->status === \App\Models\Student::STATUS_CERTIFICADO)
                                    <span class="badge rounded-pill badge-status badge-certificado">CONCLUÍDO</span>
                                    @else
                                    <span class="badge rounded-pill badge-status badge-pre-inscrito">{{ strtoupper($enrollment->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @if($enrollment->certificate)
                                    <a href="{{ route('student.certificates.pdf', $enrollment->certificate) }}" target="_blank" class="btn btn-certificate btn-sm text-white px-3 py-1.5 rounded-3">
                                        <i class="bi bi-cloud-arrow-down-fill me-1"></i> Baixar PDF
                                    </a>
                                    @elseif($enrollment->status === \App\Models\Student::STATUS_CERTIFICADO)
                                    <span class="text-soft small"><i class="bi bi-clock me-1"></i> Aguardando emissão</span>
                                    @else
                                    <span class="text-soft small"><i class="bi bi-lock-fill me-1"></i> Não liberado</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </section>
    </div>

</body>

</html>