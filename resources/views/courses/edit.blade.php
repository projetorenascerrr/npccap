@extends('layouts.app')

@section('title', 'Editar Curso - NPCCAP')
@section('page_title', 'Editar curso')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="hero-card rounded-4 p-4 p-lg-5">
            <p class="text-uppercase text-soft small mb-1">Edição</p>
            <h1 class="h3 mb-2">Editar curso</h1>
            <p class="text-soft mb-4">Atualize os dados do curso selecionado.</p>

            <div class="mb-4 d-flex align-items-center gap-2">
                <span class="text-soft">Status atual:</span>
                <span class="badge {{ $course->status === 'encerrado' ? 'text-bg-danger' : 'text-bg-success' }}">
                    {{ ucfirst($course->status ?? 'ativo') }}
                </span>
            </div>

            <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data"
                class="row g-4">
                @csrf
                @method('PUT')

                <div class="col-12 col-lg-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="image" class="form-label">Subir Imagem de Divulgação do Curso</label>
                            <input id="image" name="image" type="file" accept="image/*" class="form-control">
                            @if ($course->image_path)
                            <div class="mt-3">
                                <div class="form-text mb-2">Imagem atual</div>
                                <img src="{{ asset('storage/' . $course->image_path) }}"
                                    alt="Imagem do curso {{ $course->name }}" class="course-image w-100">
                            </div>
                            @endif
                        </div>

                        <div class="col-12">
                            <label for="name" class="form-label">Nome do curso</label>
                            <input id="name" name="name" class="form-control" value="{{ old('name', $course->name) }}"
                                required>

                            <div class="col-12 mt-3">
                                <label for="description" class="form-label">Descricao do Curso</label>
                                <textarea id="description" name="description" class="form-control"
                                    rows="4">{{ old('description', $course->description) }}</textarea>
                            </div>

                            <div class="col-md-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="active" name="active" value="1" {{ old('active', $course->active) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="active">Exibir/Ocultar curso na página inicial.</label>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="hours" class="form-label">Horas</label>
                            <input id="hours" name="hours" type="number" min="1" class="form-control"
                                value="{{ old('hours', $course->hours) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Data de inicio</label>
                            <input id="start_date" name="start_date" type="date" class="form-control"
                                value="{{ old('start_date', optional($course->start_date)->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Data de termino</label>
                            <input id="end_date" name="end_date" type="date" class="form-control"
                                value="{{ old('end_date', optional($course->end_date)->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="responsible" class="form-label">Responsavel</label>
                            <input id="responsible" name="responsible" class="form-control"
                                value="{{ old('responsible', $course->responsible) }}"
                                placeholder="Nome do responsavel pelo curso">
                        </div>

                        <div class="col-md-6">
                            <label for="minimum_frequency" class="form-label">Frequencia minima (%)</label>
                            <input id="minimum_frequency" name="minimum_frequency" type="number" min="0" max="100"
                                step="0.01" class="form-control"
                                value="{{ old('minimum_frequency', $course->minimum_frequency) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="minimum_grade" class="form-label">Nota minima</label>
                            <input id="minimum_grade" name="minimum_grade" type="number" min="0" max="10" step="0.01"
                                class="form-control" value="{{ old('minimum_grade', $course->minimum_grade) }}"
                                placeholder="Opcional">
                        </div>

                        <div class="col-md-12">
                            <label for="image_bg" class="form-label">Editar imagem de fundo do curso</label>
                            <input id="image_bg" name="image_bg" type="file" accept="image/*" class="form-control">
                            @if ($course->image_bg)
                            <div class="mt-3">
                                <div class="form-text mb-2">Imagem de fundo atual</div>
                                <img src="{{ asset('storage/' . $course->image_bg) }}"
                                    alt="Imagem de fundo do curso {{ $course->name }}" class="course-image w-100">
                            </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="verificador" class="form-label">VERIFICADOR</label>
                            <input id="verificador" name="verificador" type="text"
                                class="form-control" value="{{ old('verificador', $course->verificador) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="crc" class="form-label">CRC</label>
                            <input id="crc" name="crc" type="text"
                                class="form-control" value="{{ old('crc', $course->crc) }}">
                        </div>

                    </div>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-brand text-white">Salvar alterações</button>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-light">Cancelar</a>
                </div>
            </form>

            @if ($course->status !== 'encerrado')
            <hr class="my-4">
            <form method="POST" action="{{ route('courses.close', $course) }}"
                onsubmit="return confirm('Deseja realmente encerrar este curso? Esta ação impede novas emissões para curso ativo.');">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Encerrar curso</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection