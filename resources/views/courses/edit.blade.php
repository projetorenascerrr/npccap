@extends('layouts.app')

@section('title', 'Editar Curso - NPCCAP')
@section('page_title', 'Editar curso')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-7">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Edição</p>
            <h1 class="h3 mb-2">Editar curso</h1>
            <p class="text-soft mb-4">Atualize os dados do curso selecionado.</p>

            <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data"
                class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label for="image" class="form-label">Upload de Imagem</label>
                    <input id="image" name="image" type="file" accept="image/*" class="form-control">
                    @if ($course->image_path)
                    <div class="form-text">Imagem atual: {{ $course->image_path }}</div>
                    @endif
                </div>

                <div class="col-12">
                    <label for="name" class="form-label">Nome do curso</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $course->name) }}" required>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Descricao do Curso</label>
                    <textarea id="description" name="description" class="form-control"
                        rows="4">{{ old('description', $course->description) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label for="hours" class="form-label">Horas</label>
                    <input id="hours" name="hours" type="number" min="1" class="form-control"
                        value="{{ old('hours', $course->hours) }}">
                </div>

                <div class="col-md-6">
                    <label for="course_date" class="form-label">Data</label>
                    <input id="course_date" name="course_date" type="date" class="form-control"
                        value="{{ old('course_date', optional($course->course_date)->format('Y-m-d')) }}">
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-brand text-white">Salvar alterações</button>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-light">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection