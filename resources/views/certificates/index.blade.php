@extends('layouts.app')

@section('title', 'Certificados - NPCCAP')
@section('page_title', 'Certificados')

@section('content')
<div class="row g-4">
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
                                <a class="btn btn-sm btn-outline-success" href="{{ route('certificates.pdf', $certificate) }}" target="_blank">Compartilhar Link</a>
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
