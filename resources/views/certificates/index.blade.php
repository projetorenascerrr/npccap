@extends('layouts.app')

@section('title', 'Certificados - NPCCAP')
@section('page_title', 'Certificados')

@section('content')
<div class="row g-4">
    <div class="col-12 col-xxl-8">
        <div class="hero-card rounded-4 p-4 p-lg-5 h-100">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase text-soft small mb-1">Sistema de Certificados</p>
                    <h1 class="h3 mb-2">Cadastro de certificados</h1>
                    <p class="mb-0 text-soft">Selecione o curso, escolha o aluno daquele curso e gere o certificado em
                        PDF.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap align-items-start">
                    <a class="btn btn-outline-light" href="{{ route('courses.index') }}">Gerenciar cursos</a>
                    <a class="btn btn-brand text-white" href="{{ route('courses.create') }}">Criar novo curso</a>
                </div>
            </div>

            <form method="POST" action="{{ route('certificates.store') }}" class="row g-3" id="certificateForm">
                @csrf
                <div class="col-12 col-md-5">
                    <label for="course_id" class="form-label">Curso</label>
                    <select id="course_id" name="course_id" class="form-select" required>
                        <option value="">Selecione um curso</option>
                        @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id')==$course->id)>{{ $course->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-5">
                    <label for="student_id" class="form-label">Aluno</label>
                    <select id="student_id" name="student_id" class="form-select" required>
                        <option value="">Selecione um curso primeiro</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label for="issue_date" class="form-label">Data</label>
                    <input id="issue_date" name="issue_date" type="date" class="form-control" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-brand text-white">Salvar certificado</button>
                    <button type="button" class="btn btn-outline-light" id="clearSelection">Limpar seleção</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-xxl-4">
        <div class="panel-card rounded-4 p-4 h-100">
            <p class="text-uppercase text-soft small mb-1">Resumo</p>
            <h2 class="h5 mb-3">Estrutura ativa</h2>
            <div class="d-grid gap-3">
                <div class="border border-secondary rounded-3 p-3 bg-body-tertiary bg-opacity-10">
                    <div class="text-soft small">Cursos cadastrados</div>
                    <div class="fs-4 fw-semibold">{{ $courses->count() }}</div>
                </div>
                <div class="border border-secondary rounded-3 p-3 bg-body-tertiary bg-opacity-10">
                    <div class="text-soft small">Certificados emitidos</div>
                    <div class="fs-4 fw-semibold">{{ $certificates->count() }}</div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="border border-secondary rounded-3 p-3 bg-body-tertiary bg-opacity-10">
                <p class="text-uppercase text-soft small mb-1">Preview</p>
                <h2 class="h6 mb-2">Assinaturas do certificado</h2>
                <p class="small text-soft mb-3">A estrutura completa de preview foi movida para a pagina dedicada.</p>
                <a class="btn btn-sm btn-outline-light" href="{{ route('certificates.show') }}">Abrir preview
                    completo</a>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="panel-card rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <p class="text-uppercase text-soft small mb-1">Histórico</p>
                    <h2 class="h5 mb-0">Certificados cadastrados</h2>
                </div>
            </div>

            <form method="GET" action="{{ route('certificates.index') }}" class="row g-2 mb-3">
                <div class="col-12 col-md-9">
                    <input type="search" name="search" class="form-control" placeholder="Pesquisar por curso, aluno, CPF ou ID" value="{{ $search }}">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-light w-100">Pesquisar</button>
                    @if ($search !== '')
                    <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">Limpar</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Curso</th>
                            <th>Aluno</th>
                            <th>CPF</th>
                            <th>Data</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($certificates as $certificate)
                        <tr>
                            <td>{{ $certificate->id }}</td>
                            <td>{{ $certificate->course_name }}</td>
                            <td>{{ $certificate->student_name }}</td>
                            <td>{{ $certificate->cpf }}</td>
                            <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('certificates.pdf', $certificate) }}" target="_blank">Gerar PDF</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-soft py-4">
                                {{ $search !== '' ? 'Nenhum certificado encontrado para a pesquisa.' : 'Nenhum certificado cadastrado ainda.' }}
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
    const courses = @json($coursesForJs);
    const courseSelect = document.getElementById('course_id');
    const studentSelect = document.getElementById('student_id');
    const oldStudentId = @json(old('student_id'));
    const clearSelection = document.getElementById('clearSelection');

    function loadStudents(courseId) {
        const course = courses.find((item) => String(item.id) === String(courseId));
        studentSelect.innerHTML = '';

        if (!course || !course.students.length) {
            studentSelect.innerHTML = '<option value="">Nenhum aluno cadastrado neste curso</option>';
            return;
        }

        studentSelect.innerHTML = '<option value="">Selecione um aluno</option>';

        course.students.forEach((student) => {
            const option = document.createElement('option');
            option.value = student.id;
            option.textContent = `${student.name} - ${student.cpf}`;
            if (String(student.id) === String(oldStudentId)) {
                option.selected = true;
            }
            studentSelect.appendChild(option);
        });

    }

    courseSelect.addEventListener('change', (event) => loadStudents(event.target.value));
    clearSelection.addEventListener('click', () => {
        courseSelect.value = '';
        studentSelect.innerHTML = '<option value="">Selecione um curso primeiro</option>';
    });

    if (courseSelect.value) {
        loadStudents(courseSelect.value);
    }

</script>
@endpush
