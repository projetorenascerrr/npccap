@extends('layouts.app')

@section('title', 'Curso - NPCCAP')
@section('page_title', $course->name)

@section('content')
<div class="row g-4">
    <div class="col-12 col-xl-5">
        <div class="hero-card rounded-4 p-4 h-100">
            <p class="text-uppercase text-soft small mb-1">Curso</p>
            <h1 class="h3 mb-2">{{ $course->name }}</h1>
            <p class="text-soft mb-4">Adicione os alunos a este curso.
            </p>

            <form method="POST" action="{{ route('courses.students.store', $course) }}" class="row g-3">
                @csrf
                <div class="col-12">
                    <label for="name" class="form-label">Nome do aluno</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="col-12">
                    <label for="cpf" class="form-label">CPF</label>
                    <input id="cpf" name="cpf" class="form-control" value="{{ old('cpf') }}"
                        placeholder="000.000.000-00" required>
                </div>

                <div class="col-12">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}"
                        placeholder="aluno@exemplo.com" required>
                </div>

                <div class="col-12 col-md-4">
                    <label for="frequency" class="form-label">Frequencia (%)</label>
                    <input id="frequency" name="frequency" type="number" min="0" max="100" step="0.01"
                        class="form-control" value="{{ old('frequency') }}" placeholder="75">
                </div>

                <div class="col-12 col-md-4">
                    <label for="grade" class="form-label">Nota</label>
                    <input id="grade" name="grade" type="number" min="0" max="10" step="0.01" class="form-control"
                        value="{{ old('grade') }}" placeholder="7.0">
                </div>

                <div class="col-12 col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="inscrito" @selected(old('status')==='inscrito' )>Inscrito</option>
                        <option value="pre-inscrito" @selected(old('status')==='pre-inscrito' )>Pre-inscrito</option>
                        <option value="confirmado" @selected(old('status')==='confirmado' )>Confirmado</option>
                        <option value="certificado_emitido" @selected(old('status')==='certificado_emitido' )>
                            Certificado Emitido</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-brand text-white">Adicionar aluno</button>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-light">Voltar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-xl-7">
        <div class="panel-card rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <p class="text-uppercase text-soft small mb-1">Alunos</p>
                    <h2 class="h5 mb-0">Alunos do curso</h2>
                </div>
                <span class="badge text-bg-primary">{{ $students->count() }} alunos</span>
            </div>

            <form method="GET" action="{{ route('courses.show', $course) }}" class="row g-2 mb-3">
                <div class="col-12 col-md-9">
                    <input type="search" name="search" class="form-control"
                        placeholder="Pesquisar aluno por nome ou CPF" value="{{ $search }}">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-light w-100">Pesquisar</button>
                    @if ($search !== '')
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">Limpar</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>CPF</th>
                            <th>EMIÇÃO</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->cpf }}</td>
                            <td>
                                @if ($student->certificate)
                                <span class="badge text-bg-success text-uppercase">Emitido</span>
                                @else
                                <form method="POST" action="{{ route('certificates.store') }}">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <input type="hidden" name="issue_date" value="{{ date('Y-m-d') }}">
                                    <button type="submit" class="btn btn-brand text-white">Emitir Certificado</button>
                                </form>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a class="btn btn-sm btn-outline-warning" href="{{ route('courses.students.edit', [$course, $student]) }}">Editar</a>
                                    <form method="POST" action="{{ route('courses.students.destroy', [$course, $student]) }}" class="d-inline" onsubmit="return confirm('Deseja realmente remover o aluno deste curso?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-soft py-4">
                                {{ $search !== '' ? 'Nenhum aluno encontrado para a pesquisa.' : 'Nenhum aluno
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

@push('scripts')
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
@endpush