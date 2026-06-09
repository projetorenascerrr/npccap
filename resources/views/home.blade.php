@extends('layouts.app')

@section('title', 'Home - NPCCAP')
@section('page_title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-12 col-xl-3">
        <div class="hero-card rounded-4 p-4 h-100">
            <p class="text-uppercase text-soft small mb-1">Visao Geral</p>
            <h1 class="h3 mb-2">Painel do sistema</h1>
            <p class="text-soft mb-4">Acompanhe os principais numeros e acesse os modulos rapidamente.</p>

            <div class="d-grid gap-2">
                <a class="btn btn-brand text-white" href="{{ route('courses.create') }}">
                    <i class="bi bi-plus-lg"></i> Novo curso
                </a>
                <a class="btn btn-outline-light" href="{{ route('certificates.show') }}">
                    Emitir certificados
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-9">
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="panel-card rounded-4 p-3 h-100">
                    <p class="text-soft small mb-1">Cursos cadastrados</p>
                    <h2 class="h3 mb-0">{{ $stats['courses_total'] }}</h2>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="panel-card rounded-4 p-3 h-100">
                    <p class="text-soft small mb-1">Cursos ativos</p>
                    <h2 class="h3 mb-0">{{ $stats['courses_active'] }}</h2>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="panel-card rounded-4 p-3 h-100">
                    <p class="text-soft small mb-1">Alunos</p>
                    <h2 class="h3 mb-0">{{ $stats['students_total'] }}</h2>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="panel-card rounded-4 p-3 h-100">
                    <p class="text-soft small mb-1">Certificados emitidos</p>
                    <h2 class="h3 mb-0">{{ $stats['certificates_total'] }}</h2>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="panel-card rounded-4 p-3 h-100">
                    <p class="text-soft small mb-1">Assinaturas</p>
                    <h2 class="h5 mb-0">
                        {{ $stats['signatures_configured'] ? 'Configuradas' : 'Nao configuradas' }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="panel-card rounded-4 p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h5 mb-0">Cursos recentes</h3>
                        <a href="{{ route('courses.index') }}" class="small text-decoration-none">Ver todos</a>
                    </div>

                    <ul class="list-group list-group-flush">
                        @forelse ($recentCourses as $course)
                        <li
                            class="list-group-item bg-transparent border-secondary px-0 text-light d-flex justify-content-between align-items-center">
                            <span>{{ $course->name }}</span>
                            <span class="badge text-bg-secondary">{{ $course->students_count }} alunos</span>
                        </li>
                        @empty
                        <li class="list-group-item bg-transparent border-secondary px-0 text-soft">Nenhum curso
                            cadastrado.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="panel-card rounded-4 p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h5 mb-0">Certificados recentes</h3>
                        <a href="{{ route('certificates.show') }}" class="small text-decoration-none">Abrir modulo</a>
                    </div>

                    <ul class="list-group list-group-flush">
                        @forelse ($recentCertificates as $certificate)
                        <li class="list-group-item bg-transparent border-secondary px-0 text-light">
                            <div class="fw-semibold">{{ $certificate->student_name }}</div>
                            <div class="small text-soft">{{ $certificate->course_name }}</div>
                        </li>
                        @empty
                        <li class="list-group-item bg-transparent border-secondary px-0 text-soft">Nenhum certificado
                            emitido.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection