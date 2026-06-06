@extends('layouts.app')

@section('title', 'Preview do Certificado - NPCCAP')
@section('page_title', 'Preview do certificado')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10 col-xxl-10">
        <div class="panel-card rounded-4 p-4 p-lg-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <p class="text-uppercase text-soft small mb-1">Preview</p>
                    <h2 class="h5 mb-0">Assinaturas do certificado</h2>
                </div>
                <a class="btn btn-sm btn-outline-light" href="{{ route('signature.index') }}">Editar</a>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-5">
                    <label for="preview_course_id" class="form-label">Curso</label>
                    <select id="preview_course_id" class="form-select">
                        <option value="">Selecione um curso</option>
                        @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-5">
                    <label for="preview_student_id" class="form-label">Aluno</label>
                    <select id="preview_student_id" class="form-select">
                        <option value="">Selecione um curso primeiro</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label for="preview_issue_date" class="form-label">Data</label>
                    <input id="preview_issue_date" type="date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="border border-warning-subtle rounded-3 p-3 mb-3 bg-body-tertiary bg-opacity-10">
                <div class="small text-soft mb-2">Previa do certificado</div>
                <div id="previewBg" class="text-center border border-secondary rounded-3 p-3 position-relative overflow-hidden" style="background-size: cover; background-position: center; background-repeat: no-repeat;">
                    <div id="previewOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none" style="background: rgba(254,251,243,0.72); z-index: 0;"></div>
                    <div class="position-relative" style="z-index: 1;">
                        <div class="fw-semibold" style="letter-spacing: 0.08em;">CERTIFICADO</div>
                        <div class="small text-soft mb-2">A Secretaria de Estado da Justiça e da Cidadania por intermédio do Núcleo Pedagógico de Capacitação Continuada, confere a</div>
                        <div id="previewStudentName" class="fw-semibold fs-6 my-1">Aluno selecionado</div>
                        <div class="small">concluiu com aproveitamento o curso:</div>
                        <div id="previewCourseName" class="fw-semibold mb-1">Curso selecionado</div>
                        <div id="previewIssueDate" class="small text-soft mb-3">Emitido em --/--/----</div>

                        <div class="row g-3 mt-2">
                            <div class="col-6 text-center">
                                <div class="border-top border-secondary mb-2"></div>
                                <div class="small" style="white-space: pre-line;">{{ $signature?->ass1 ?: 'Assinatura 1 não
                                configurada.' }}</div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="border-top border-secondary mb-2"></div>
                                <div class="small" style="white-space: pre-line;">{{ $signature?->ass2 ?: 'Assinatura 2 não
                                configurada.' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a class="btn btn-outline-light" href="{{ route('certificates.index') }}">Voltar</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const courses = @json($coursesForJs);
    const courseSelect = document.getElementById('preview_course_id');
    const studentSelect = document.getElementById('preview_student_id');
    const issueDateInput = document.getElementById('preview_issue_date');
    const previewStudentName = document.getElementById('previewStudentName');
    const previewCourseName = document.getElementById('previewCourseName');
    const previewIssueDate = document.getElementById('previewIssueDate');
    const previewBg = document.getElementById('previewBg');
    const previewOverlay = document.getElementById('previewOverlay');

    function loadStudents(courseId) {
        const course = courses.find((item) => String(item.id) === String(courseId));
        studentSelect.innerHTML = '';

        if (!course || !course.students.length) {
            studentSelect.innerHTML = '<option value="">Nenhum aluno cadastrado neste curso</option>';
            updatePreview();
            return;
        }

        studentSelect.innerHTML = '<option value="">Selecione um aluno</option>';

        course.students.forEach((student) => {
            const option = document.createElement('option');
            option.value = student.id;
            option.textContent = `${student.name} - ${student.cpf}`;
            studentSelect.appendChild(option);
        });

        updatePreview();
    }

    function formatDate(dateValue) {
        if (!dateValue) {
            return '--/--/----';
        }

        const [year, month, day] = dateValue.split('-');
        if (!year || !month || !day) {
            return '--/--/----';
        }

        return `${day}/${month}/${year}`;
    }

    function updatePreview() {
        const selectedCourseOption = courseSelect.options[courseSelect.selectedIndex];
        const selectedStudentOption = studentSelect.options[studentSelect.selectedIndex];

        previewCourseName.textContent = selectedCourseOption && selectedCourseOption.value ? selectedCourseOption.textContent : 'Curso selecionado';

        // Update background image
        const selectedCourse = courses.find((item) => String(item.id) === String(courseSelect.value));
        if (selectedCourse && selectedCourse.image_url) {
            previewBg.style.backgroundImage = `url('${selectedCourse.image_url}')`;
            previewOverlay.classList.remove('d-none');
        } else {
            previewBg.style.backgroundImage = '';
            previewOverlay.classList.add('d-none');
        }

        if (selectedStudentOption && selectedStudentOption.value) {
            previewStudentName.textContent = selectedStudentOption.textContent.split(' - ')[0];
        } else {
            previewStudentName.textContent = 'Aluno selecionado';
        }

        previewIssueDate.textContent = `Emitido em ${formatDate(issueDateInput.value)}`;
    }

    courseSelect.addEventListener('change', (event) => loadStudents(event.target.value));
    studentSelect.addEventListener('change', updatePreview);
    issueDateInput.addEventListener('change', updatePreview);

    updatePreview();

</script>
@endpush
