@extends('layouts.app')

@section('title', 'Editar Curso - NPCCAP')
@section('page_title', 'Editar curso')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-7">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Edição</p>
            <h1 class="h3 mb-2">Editar nome do curso</h1>
            <p class="text-soft mb-4">Atualize o nome do curso selecionado.</p>

            <form method="POST" action="{{ route('courses.update', $course) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label for="name" class="form-label">Nome do curso</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $course->name) }}" required>
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
