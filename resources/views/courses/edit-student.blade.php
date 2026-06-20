@extends('layouts.app')

@section('title', 'Editar Aluno - NPCCAP')
@section('page_title', 'Editar aluno')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Edição</p>
            <h1 class="h3 mb-2">Editar aluno do curso {{ $course->name }}</h1>
            <p class="text-soft mb-4">Atualize os dados do aluno e os criterios de aprovacao.</p>

            <form method="POST" action="{{ route('courses.students.update', [$course, $student]) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12 col-md-7">
                    <label for="name" class="form-label">Nome do aluno</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $student->name) }}"
                        required>
                </div>

                <div class="col-12 col-md-5">
                    <label for="cpf" class="form-label">CPF</label>
                    <input id="cpf" name="cpf" class="form-control" value="{{ old('cpf', $student->cpf) }}"
                        placeholder="000.000.000-00" required>
                </div>

                <div class="col-12">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" name="email" type="email" class="form-control"
                        value="{{ old('email', $student->email) }}" placeholder="aluno@exemplo.com">
                </div>

                <div class="col-12 col-md-4">
                    <label for="frequency" class="form-label">Frequencia (%)</label>
                    <input id="frequency" name="frequency" type="number" min="0" max="100" step="0.01"
                        class="form-control" value="{{ old('frequency', $student->frequency) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="grade" class="form-label">Nota</label>
                    <input id="grade" name="grade" type="number" min="0" max="10" step="0.01" class="form-control"
                        value="{{ old('grade', $student->grade) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="inscrito" @selected(old('status', $student->status) === 'inscrito')>Inscrito
                        </option>
                        <option value="pre-inscrito" @selected(old('status', $student->status) ===
                            'pre-inscrito')>Pre-inscrito</option>
                        <option value="confirmado" @selected(old('status', $student->status) ===
                            'confirmado')>Confirmado</option>
                        <option value="certificado_emitido" @selected(old('status', $student->status) ===
                            'certificado_emitido')>Certificado Emitido</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-brand text-white">Salvar alterações</button>
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-light">Cancelar</a>
                </div>
            </form>
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