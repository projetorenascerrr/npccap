<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NPCCAP | Formacao e Cursos</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .institutional-page {
            background:
                radial-gradient(circle at 8% 10%, rgba(22, 163, 74, 0.18), transparent 35%),
                radial-gradient(circle at 90% 0%, rgba(14, 165, 233, 0.2), transparent 33%),
                linear-gradient(160deg, #030712 0%, #0b1220 45%, #081225 100%);
            color: #e2e8f0;
        }

        .hero-box,
        .course-card,
        .cta-box {
            background: rgba(15, 23, 42, 0.74);
            border: 1px solid rgba(148, 163, 184, 0.22);
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.35);
            backdrop-filter: blur(7px);
        }

        .course-image {
            width: 100%;
            height: auto;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            display: block;
        }

        .course-placeholder {
            width: 100%;
            aspect-ratio: 16 / 9;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            background: linear-gradient(140deg, #0f766e, #2563eb);
        }

        .section-title {
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #7dd3fc;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .badge-status {
            background: rgba(16, 185, 129, 0.2);
            color: #a7f3d0;
            border: 1px solid rgba(52, 211, 153, 0.4);
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .fade-up {
            animation: fadeUp 500ms ease both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="institutional-page">
    <div class="container py-5">
        <section class="hero-box rounded-4 p-4 p-lg-5 mb-4 mb-lg-5 fade-up">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <a class="text-decoration-none text-reset" href="{{ route('welcome') }}">
                    <span class="section-title">NPCCAP</span>
                    <h1 class="display-5 fw-semibold mt-2 mb-3">Capacitação profissional com foco em resultados reais
                    </h1>
                    </a>
                    <p class="text-soft fs-5 mb-3">
                        Conheca os cursos disponiveis e participe do aprendizado com metodologia pratica.
                    </p>
                    @auth('student')
                    <div class="d-flex gap-2">
                        <a href="{{ route('student.dashboard') }}" class="btn btn-success btn-sm">Área do Aluno</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Sair</button>
                        </form>
                    </div>
                    @elseauth('web')
                    <div class="d-flex gap-2">
                        <a href="{{ route('courses.index') }}" class="btn btn-danger btn-sm">Painel Admin</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Sair</button>
                        </form>
                    </div>
                    @else
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-light btn-sm">Entrar</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">Cadastrar</a>
                    </div>
                    @endauth
                </div>
                <div class="col-lg-4">
                    <div class="cta-box rounded-4 p-4 text-center">
                        <p class="text-soft mb-2">Cursos disponiveis agora</p>
                        <p class="display-4 fw-bold mb-0">{{ $courses->count() }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h3 fw-semibold mb-0">Cursos ativos</h2>
                <span class="text-soft small">Ocultamos automaticamente cursos encerrados/finalizados</span>
            </div>

            @if($courses->isEmpty())
            <div class="course-card rounded-4 p-5 text-center fade-up">
                <h3 class="h4 mb-2">Nenhum curso ativo no momento</h3>
                <p class="text-soft mb-0">Novas turmas serao publicadas em breve.</p>
            </div>
            @else
            <div class="row g-4">
                @foreach($courses as $course)
                <div class="col-12 col-md-6 col-xl-4 fade-up">
                    <article class="course-card rounded-4 h-100 overflow-hidden">
                        @if($course->image_path)
                        <img class="course-image" src="{{ asset('storage/' . $course->image_path) }}"
                            alt="Imagem do curso {{ $course->name }}">
                        @else
                        <div class="course-placeholder d-flex align-items-center justify-content-center">
                            <i class="bi bi-mortarboard-fill fs-1 text-white"></i>
                        </div>
                        @endif

                        <div class="p-4 d-flex flex-column h-100">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge rounded-pill badge-status">ATIVO</span>
                                @if($course->hours)
                                <span class="small text-soft">{{ $course->hours }}h</span>
                                @endif
                            </div>

                            <h3 class="h5 fw-semibold mb-2">{{ $course->name }}</h3>
                            <p class="text-soft mb-3">
                                {{ $course->description ?: 'Curso com conteudo atualizado e aplicacao pratica para o
                                mercado.' }}
                            </p>

                            @auth('student')
                            <a href="{{ route('student.courses.enroll', $course) }}" class="btn btn-outline-success btn-strong btn-sm mb-3">
                                INSCREVER-SE
                            </a>
                            @else
                            <a href="{{ route('login', ['course_id' => $course->id]) }}" class="btn btn-outline-success btn-strong btn-sm mb-3">
                                INSCREVER-SE
                            </a>
                            @endauth

                            <div class="mt-auto small text-soft">
                                @if($course->start_date)
                                <div><strong class="text-light">Inicio:</strong> {{ $course->start_date->format('d/m/Y')
                                    }}</div>
                                @endif
                                @if($course->end_date)
                                <div><strong class="text-light">Previsao de termino:</strong> {{
                                    $course->end_date->format('d/m/Y') }}</div>
                                @endif
                                @if($course->responsible)
                                <div><strong class="text-light">Responsavel:</strong> {{ $course->responsible }}</div>
                                @endif
                            </div>

                        </div>
                    </article>
                </div>
                @endforeach
            </div>
            @endif
        </section>
    </div>
</body>

</html>