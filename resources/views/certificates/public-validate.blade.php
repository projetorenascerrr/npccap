<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Certificado – NPCCAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0f1117; color: #e8e2d5; }
        .valid-badge { background: #166534; color: #bbf7d0; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card bg-dark border-secondary text-center p-5" style="max-width:540px;width:100%">
        <div class="mb-4">
            <img src="{{ asset('images/logo_sejuc.svg') }}" alt="Logo" class="img-fluid" style="height: 32px;">
        </div>
        <div class="mb-3">
            <span class="badge valid-badge fs-6 px-4 py-2">✔ Certificado Válido</span>
        </div>
        <h1 class="h4 mb-4">Verificação de Autenticidade</h1>

        <dl class="text-start row gy-2">
            <dt class="col-5 text-secondary">Participante</dt>
            <dd class="col-7">{{ $certificate->student_name }}</dd>

            <dt class="col-5 text-secondary">Curso / Evento</dt>
            <dd class="col-7">{{ $certificate->course_name }}</dd>

            <dt class="col-5 text-secondary">Carga horária</dt>
            <dd class="col-7">{{ $certificate->course?->hours ?? '–' }} h</dd>

            <dt class="col-5 text-secondary">Data de emissão</dt>
            <dd class="col-7">{{ $certificate->issue_date->format('d/m/Y') }}</dd>

            <dt class="col-5 text-secondary">Código de validação</dt>
            <dd class="col-7"><code>{{ $certificate->validation_code }}</code></dd>
        </dl>

        <p class="text-secondary small mt-4 mb-0">
            Este certificado foi emitido pelo Núcleo Pedagógico de Capacitação Continuada (NPCCAP)
            e sua autenticidade foi verificada nesta página.
        </p>
    </div>
</body>
</html>
