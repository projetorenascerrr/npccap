@extends('layouts.app')

@section('title', 'Novo Curso - NPCCAP')
@section('page_title', 'Criar novo curso')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-7">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Cadastro</p>
            <h1 class="h3 mb-2">Criar novo curso</h1>
            <p class="text-soft mb-4">Depois de salvar o curso, você poderá adicionar os alunos dele.</p>

            <form method="POST" action="{{ route('courses.store') }}" class="row g-3">
                @csrf
                <div class="col-12">
                    <label for="name" class="form-label">Nome do curso</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name') }}" required>
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
