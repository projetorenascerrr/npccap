@extends('layouts.app')

@section('title', 'Editar Aluno - NPCCAP')
@section('page_title', 'Editar aluno')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Edição</p>
            <h1 class="h3 mb-2">Editar aluno do curso {{ $course->name }}</h1>
            <p class="text-soft mb-4">Atualize o nome e CPF do aluno.</p>

            <form method="POST" action="{{ route('courses.students.update', [$course, $student]) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12 col-md-7">
                    <label for="name" class="form-label">Nome do aluno</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
                </div>

                <div class="col-12 col-md-5">
                    <label for="cpf" class="form-label">CPF</label>
                    <input id="cpf" name="cpf" class="form-control" value="{{ old('cpf', $student->cpf) }}" placeholder="000.000.000-00" required>
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
