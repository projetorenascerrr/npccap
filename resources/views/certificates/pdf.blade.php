<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            font-family: Georgia, "Times New Roman", serif;
            color: #2c2a26;
        }

        /* Background image covering the full A4 landscape page */
        .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            z-index: 0;
        }

        .bg img {
            width: 297mm;
            height: 210mm;
            display: block;
        }

        /* Semi-transparent overlay so text stays readable */
        .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            background: rgba(254, 251, 243, 0.72);
            z-index: 1;
        }

        /* Gold border frame */
        .certificate {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            border: 5mm solid #b68a3d;
            z-index: 2;
        }

        .inner {
            position: absolute;
            top: 5mm;
            left: 5mm;
            right: 5mm;
            bottom: 5mm;
            border: 1.2mm solid #d8b46e;
            padding: 10mm 14mm 32mm;
            text-align: center;
        }

        h1 {
            font-size: 16mm;
            margin: 0 0 2mm;
            letter-spacing: 1.5mm;
            color: #8a652a;
        }

        h2 {
            margin: 0 0 6mm;
            font-size: 7mm;
            font-weight: normal;
            letter-spacing: 1mm;
            text-transform: uppercase;
        }

        p {
            margin: 3mm 0;
            font-size: 5mm;
            line-height: 1.6;
        }

        .name {
            font-size: 9mm;
            margin: 6mm 0 1mm;
            font-weight: bold;
            color: #5a4219;
        }

        .course {
            font-size: 6.5mm;
            font-weight: bold;
            color: #7a5a20;
            margin-top: 1mm;
        }

        .meta {
            margin-top: 8mm;
            font-size: 4.5mm;
        }

        /* Signatures anchored to bottom of the inner frame */
        .signatures {
            position: absolute;
            left: 5mm;
            right: 5mm;
            bottom: 8mm;
            padding: 0 16mm;
            font-size: 4.2mm;
            white-space: pre-line;
            display: table;
            width: calc(297mm - 10mm - 32mm);
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .line {
            width: 80mm;
            margin: 0 auto 2mm;
            border-top: 0.4mm solid #2c2a26;
        }

    </style>
</head>

<body>

    @if ($backgroundPath)
    <div class="bg">
        <img src="{{ $backgroundPath }}" alt="">
    </div>
    <div class="bg-overlay"></div>
    @endif

    <div class="certificate">
        <div class="inner">
            <h1>CERTIFICADO</h1>
            <p>A Secretaria de Estado da Justiça e da Cidadania por intermédio do Núcleo Pedagógico de Capacitação Continuada, confere a</p>
            <div class="name">{{ $certificate->student_name }}</div>
            <p>CPF: {{ $certificate->cpf }}</p>
            <p>concluiu com aproveitamento o curso:</p>
            <div class="course">{{ $certificate->course_name }}</div>

            <div class="meta">
                Emitido em {{ $certificate->issue_date->format('d/m/Y') }}.
            </div>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <div class="line"></div>
                {!! nl2br(e($signature?->ass1 ?: 'Assinatura 1 não configurada.')) !!}
            </div>
            <div class="signature-box">
                <div class="line"></div>
                {!! nl2br(e($signature?->ass2 ?: 'Assinatura 2 não configurada.')) !!}
            </div>
        </div>
    </div>

</body>

</html>
