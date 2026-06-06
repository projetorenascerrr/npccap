@extends('layouts.app')

@section('title', 'Aluno - NPCCAP')
@section('page_title', $student->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Aluno</p>
            <h1 class="h3 mb-2">{{ $student->name }}</h1>
            <p class="text-soft mb-4">Curso: <strong>{{ $course->name }}</strong></p>

            <dl class="row mb-4">
                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9">{{ $student->name }}</dd>

                <dt class="col-sm-3">CPF</dt>
                <dd class="col-sm-9">{{ $student->cpf }}</dd>
            </dl>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('courses.students.edit', [$course, $student]) }}"
                    class="btn btn-brand text-white">Editar</a>
                <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-light">Voltar ao curso</a>
            </div>
        </div>
    </div>
</div>
@endsection