@extends('layouts.app')

@section('title', 'Cursos - NPCCAP')
@section('page_title', 'Cursos')

@section('content')
<div class="row g-4">
    <div class="col-12 col-xl-3">
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

    <div class="col-12 col-xl-9">
        <div class="panel-card rounded-4 p-4">
            <form method="GET" action="{{ route('courses.index') }}" class="row g-2 mb-3">
                <div class="col-12 col-md-9">
                    <input type="search" name="search" class="form-control"
                        placeholder="Pesquisar por curso, aluno ou CPF" value="{{ $search }}">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-light w-100">Pesquisar</button>
                    @if ($search !== '')
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Limpar</a>
                    @endif
                </div>
            </form>

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
                            <td class="text-uppercase">
                                {{ $course->name }}
                                @if (($course->status ?? 'ativo') === 'encerrado')
                                <span class="badge text-bg-danger ms-2">Encerrado</span>
                                @endif
                            </td>
                            <td>{{ $course->students_count }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-info"
                                    href="{{ route('courses.show', $course) }}">Abrir</a>
                                <a class="btn btn-sm btn-outline-warning"
                                    href="{{ route('courses.edit', $course) }}">Editar</a>
                                @if (($course->status ?? 'ativo') !== 'encerrado')
                                <form method="POST" action="{{ route('courses.close', $course) }}" class="d-inline"
                                    onsubmit="return confirm('Deseja realmente encerrar este curso?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Encerrar</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-soft py-4">
                                {{ $search !== '' ? 'Nenhum curso encontrado para a pesquisa.' : 'Nenhum curso
                                cadastrado ainda.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection