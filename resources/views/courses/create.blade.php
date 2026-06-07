@extends('layouts.app')

@section('title', 'Novo Curso - NPCCAP')
@section('page_title', 'Criar novo curso')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Cadastro</p>
            <h1 class="h3 mb-2">Criar novo curso</h1>
            <p class="text-soft mb-4">Depois de salvar o curso, você poderá adicionar os alunos dele.</p>

            <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data" class="row g-4">
                @csrf

                <div class="col-12 col-lg-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="image" class="form-label">Upload de Imagem</label>
                            <input id="image" name="image" type="file" accept="image/*" class="form-control">
                        </div>

                        <div class="col-12">
                            <label for="name" class="form-label">Nome do curso</label>
                            <input id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="description" class="form-label">Descricao do Curso</label>
                            <textarea id="description" name="description" class="form-control"
                                rows="4">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="hours" class="form-label">Horas</label>
                            <input id="hours" name="hours" type="number" min="1" class="form-control"
                                value="{{ old('hours') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="course_date" class="form-label">Data</label>
                            <input id="course_date" name="course_date" type="date" class="form-control"
                                value="{{ old('course_date') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="minimum_frequency" class="form-label">Frequencia minima (%)</label>
                            <input id="minimum_frequency" name="minimum_frequency" type="number" min="0" max="100"
                                step="0.01" class="form-control" value="{{ old('minimum_frequency', 75) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="minimum_grade" class="form-label">Nota minima</label>
                            <input id="minimum_grade" name="minimum_grade" type="number" min="0" max="10" step="0.01"
                                class="form-control" value="{{ old('minimum_grade') }}" placeholder="Opcional">
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-brand text-white">Salvar curso</button>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-light">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection