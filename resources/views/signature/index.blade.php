@extends('layouts.app')

@section('title', 'Assinaturas - NPCCAP')
@section('page_title', 'Assinaturas')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Configuracao</p>
            <h1 class="h3 mb-2">Assinaturas</h1>
            <p class="text-soft mb-4">Preencha os dois campos abaixo com as assinaturas que serao usadas no sistema.</p>

            <form method="POST" action="{{ route('signature.store') }}" class="row g-3">
                @csrf

                <div class="col-12">
                    <label for="ass1" class="form-label">Ass1</label>
                    <textarea id="ass1" name="ass1" rows="3" class="form-control"
                        required>{{ old('ass1', $signature?->ass1) }}</textarea>
                </div>

                <div class="col-12">
                    <label for="ass2" class="form-label">Ass2</label>
                    <textarea id="ass2" name="ass2" rows="3" class="form-control"
                        required>{{ old('ass2', $signature?->ass2) }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-brand text-white">Salvar assinaturas</button>
                    <a href="{{ route('certificates.index') }}" class="btn btn-outline-light">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection