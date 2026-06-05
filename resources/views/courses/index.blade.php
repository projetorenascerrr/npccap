@extends('layouts.app')

@section('title', 'Cursos - NPCCAP')
@section('page_title', 'Cursos')

@section('content')
<div class="row g-4">
    <div class="col-12 col-xl-4">
        <div class="hero-card rounded-4 p-4 h-100">
            <p class="text-uppercase text-soft small mb-1">Gestão</p>
            <h1 class="h3 mb-2">Cursos cadastrados</h1>
            <p class="text-soft">Crie um novo curso e entre no cadastro dos alunos daquele curso.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-brand text-white" href="{{ route('courses.create') }}">Criar novo curso</a>
                <a class="btn btn-outline-light" href="{{ route('certificates.index') }}">Certificados</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="panel-card rounded-4 p-4">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Alunos</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                        <tr>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->students_count }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('courses.show', $course) }}">Abrir</a>
                                <a class="btn btn-sm btn-outline-warning" href="{{ route('courses.edit', $course) }}">Editar</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-soft py-4">Nenhum curso cadastrado ainda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
