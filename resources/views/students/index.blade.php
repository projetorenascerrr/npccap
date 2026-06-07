@extends('layouts.app')

@section('title', 'Alunos - NPCCAP')
@section('page_title', 'Alunos')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="panel-card rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <p class="text-uppercase text-soft small mb-1">Consulta Global</p>
                    <h1 class="h4 mb-0">Alunos cadastrados</h1>
                </div>
                <a class="btn btn-outline-light" href="{{ route('courses.index') }}">Ir para cursos</a>
            </div>

            <form method="GET" action="{{ route('students.index') }}" class="row g-2 mb-3">
                <div class="col-12 col-md-10">
                    <input type="search" name="search" class="form-control"
                        placeholder="Pesquisar por nome, CPF, e-mail ou curso" value="{{ $search }}">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-light w-100">Pesquisar</button>
                    @if ($search !== '')
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Limpar</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Curso</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->cpf }}</td>
                            <td>{{ $student->email ?: '—' }}</td>
                            <td>{{ $student->course?->name ?: '—' }}</td>
                            <td>
                                <span class="badge text-bg-secondary">{{ $student->status ?: 'inscrito' }}</span>
                            </td>
                            <td class="text-end">
                                @if ($student->course)
                                <a class="btn btn-sm btn-outline-info"
                                    href="{{ route('courses.students.show', [$student->course, $student]) }}"><i class="bi bi-eye" title="Visualizar"></i></a>
                                <a class="btn btn-sm btn-outline-warning"
                                    href="{{ route('courses.students.edit', [$student->course, $student]) }}"><i class="bi bi-pencil" title="Editar"></i></a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-soft py-4">
                                {{ $search !== '' ? 'Nenhum aluno encontrado para a pesquisa.' : 'Nenhum aluno
                                cadastrado ainda.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($students->hasPages())
            <div class="mt-3">
                {{ $students->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection