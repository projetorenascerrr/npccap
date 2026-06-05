<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            color: #2c2a26;
        }

        .certificate {
            width: 297mm;
            height: 210mm;
            padding: 16mm;
            border: 5mm solid #b68a3d;
            box-sizing: border-box;
            position: relative;
            background: #fefbf3;
        }

        .inner {
            width: 100%;
            height: 100%;
            border: 1.2mm solid #d8b46e;
            padding: 12mm;
            text-align: center;
            box-sizing: border-box;
        }

        h1 {
            font-size: 16mm;
            margin: 0;
            letter-spacing: 1.5mm;
            color: #8a652a;
        }

        h2 {
            margin: 3mm 0 8mm;
            font-size: 7mm;
            font-weight: normal;
            letter-spacing: 1mm;
            text-transform: uppercase;
        }

        p {
            margin: 4mm 0;
            font-size: 5.1mm;
            line-height: 1.6;
        }

        .name {
            font-size: 9mm;
            margin: 8mm 0 2mm;
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
            margin-top: 10mm;
            font-size: 4.5mm;
        }

        .signature {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 16mm;
            text-align: center;
            font-size: 4.2mm;
        }

        .line {
            width: 70mm;
            margin: 0 auto 2mm;
            border-top: 0.4mm solid #2c2a26;
        }

    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner">
            <h1>CERTIFICADO</h1>
            <h2>NPCCAP</h2>

            <p>Certificamos que</p>
            <div class="name">{{ $certificate->student_name }}</div>
            <p>CPF: {{ $certificate->cpf }}</p>
            <p>concluiu com aproveitamento o curso:</p>
            <div class="course">{{ $certificate->course_name }}</div>

            <div class="meta">
                Emitido em {{ $certificate->issue_date->format('d/m/Y') }}.
            </div>
        </div>

        <div class="signature">
            <div class="line"></div>
            Coordenacao NPCCAP
        </div>
    </div>
</body>
</html>
