@php
// Format date in Portuguese
$months = [
1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
];
$day = $certificate->issue_date->format('d');
$month = $months[(int)$certificate->issue_date->format('m')];
$year = $certificate->issue_date->format('Y');
$issueDateFormatted = "Boa Vista-RR, {$day} de {$month} de {$year}.";

// Retrieve Verificador and CRC from the associated course
$verificador = $certificate->course->verificador ?? '';
$crc = $certificate->course->crc ?? '';

// Generate SEI QR Code (svg inline)
$rawSeiQrCode = SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(120)->generate('https://sei.rr.gov.br/sei/controlador_externo.php?acao=documento_conferir&codigo_verificador=' . urlencode($verificador) . '&codigo_crc=' . urlencode($crc) . '&hash_download=230890f57c3f84f81cb3c81ec4365c7e2c6f2b8dd21e889796abc10c7dd37ae12bb8039dcd8ea5400288825eebd46d0febdfe53e13cea2a29b4e63318099df34&visualizacao=1&id_orgao_acesso_externo=0');
$seiQrCodeSvg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', (string)$rawSeiQrCode);

// Resolve and base64-encode Roraima coat of arms image
$brasaoPath = public_path('images/brasao_roraima.png');
$brasaoData = '';
if (file_exists($brasaoPath)) {
$type = pathinfo($brasaoPath, PATHINFO_EXTENSION);
$data = file_get_contents($brasaoPath);
$brasaoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

// Resolve and base64-encode star image
$starPath = public_path('images/star.png');
$starData = '';
if (file_exists($starPath)) {
$type = pathinfo($starPath, PATHINFO_EXTENSION);
$data = file_get_contents($starPath);
$starData = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

// Parse signatures into lines for individual styling
$linesAss1 = ($signature && !empty($signature->ass1)) ? array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $signature->ass1))))) : ['Assinatura 1 não configurada.'];
$linesAss2 = ($signature && !empty($signature->ass2)) ? array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $signature->ass2))))) : ['Assinatura 2 não configurada.'];
@endphp
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Libre+Baskerville:ital,wght@0,400..700;1,400..700&display=swap');

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
            background-color: #ffffff;
            font-family: "Libre Baskerville", serif;
        }
   
        body,
        .certificate,
        .certificate *,
        .certificate-back,
        .certificate-back * {
            font-family: "Libre Baskerville", serif !important;
            color: #2c2a26;
        }

        .page {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        .page-break {
            page-break-before: always;
            height: 0;
            line-height: 0;
        }

        /* Front Page Styles */
        .certificate {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
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
            z-index: 1;
        }

        .inner {
            position: absolute;
            top: 68mm;
            left: 5mm;
            right: 5mm;
            bottom: 5mm;
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
            left: 2mm;
            right: 2mm;
            bottom: 50mm;
            padding: 0 0;
            display: table;
            width: calc(292mm - 0mm - 0mm);
        }

        .signature-box {
            display: table-cell;
            width: 70%;
            text-align: center;
            vertical-align: top;
        }

        .line {
            width: 60mm;
            margin: 0 auto 2mm;
            border-top: 0.1mm solid #2c2a26;
        }

        .sig-name {
            font-size: 4.6mm;
            font-weight: bold;
            margin: 0 0 1 0;
            line-height: 1;
        }

        .sig-role {
            font-size: 3.4mm;
            font-weight: normal;
            margin: 0 0 1 0;
            line-height: 1;
        }

        .sig-decree {
            font-size: 3.2mm;
            font-weight: normal;
            margin: 0;
            line-height: 1;
        }

        .qr-area {
            position: absolute;
            right: 8mm;
            bottom: 8mm;
            text-align: center;
            font-size: 3mm;
            color: #5a4219;
        }

        .validation-code {
            margin-top: 8mm;
            font-size: 3.5mm;
            color: #7a5a20;
            letter-spacing: 0.5mm;
        }

        /* Second Page / Verso Styles */
        .certificate-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            padding: 0;
            background-color: #ffffff;
        }

        .back-border {
            border: 0.5mm solid #2c2a26;
            border-radius: 6mm;
            width: 260mm;
            height: 150mm;
            position: absolute;
            top: 28mm;
            left: 18mm;
            padding: 0;
        }

        .back-left {
            position: absolute;
            top: 8mm;
            left: 10mm;
            width: 170mm;
            height: 174mm;
        }

        .back-divider {
            position: absolute;
            top: 8mm;
            left: 185mm;
            bottom: 8mm;
            width: 0.4mm;
            border-left: 0.4mm solid #2c2a26;
        }

        .back-right {
            position: absolute;
            top: 8mm;
            left: 185mm;
            width: 77mm;
            height: 174mm;
            text-align: center;
        }

        .back-title {
            font-size: 7mm;
            font-weight: bold;
            text-decoration: underline;
            text-align: center;
            margin-top: 5mm;
            margin-bottom: 10mm;
            letter-spacing: 1mm;
        }

        .back-text {
            font-size: 4.2mm;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 12mm;
            color: #2c2a26;
        }

        .back-text .url {
            font-family: "Libre Baskerville", serif !important;
            color: #2c2a26;
            text-decoration: none;
        }

        .back-info-box {
            margin-left: 2mm;
            margin-bottom: 12mm;
        }

        .back-info-row {
            font-size: 5mm;
            font-weight: bold;
            margin-bottom: 4mm;
            color: #2c2a26;
        }

        .back-date {
            font-size: 5mm;
            margin-left: 2mm;
            margin-bottom: 20mm;
            color: #2c2a26;
        }

        .back-footer {
            position: absolute;
            bottom: 40mm;
            left: 0;
            width: 170mm;
            text-align: center;
        }

        .back-footer-title {
            font-size: 3.8mm;
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .back-footer-text {
            font-size: 2.8mm;
            line-height: 1.3;
        }

        /* Right Column Styles */
        .back-qr-box {
            margin-top: 15mm;
            text-align: center;
        }

        .back-qr-code {
            text-align: center;
        }

        .back-qr-text {
            margin-top: 2.5mm;
            font-family: "Libre Baskerville", serif !important;
            font-size: 2.6mm;
            color: #2c2a26;
        }

        .back-brasao-box {
            position: absolute;
            bottom: 45mm;
            left: 0;
            width: 77mm;
            text-align: center;
        }

        .back-brasao-img {
            width: 40mm;
            height: auto;
            display: inline-block;
        }
    </style>
</head>

<body>

    <!-- First Page (Frente) -->
    <div class="page">
        @if ($backgroundPath)
        <div class="bg">
            <img src="{{ $backgroundPath }}" alt="">
        </div>
        <div class="bg-overlay"></div>
        @endif

        <div class="certificate">
            <div class="inner">
                <p><i>A Secretaria de Estado da Justiça e da Cidadania<br>por intermédio do Núcleo Pedagógico de Capacitação
                        Continuada, confere a</i></p>
                <div class="name text-uppercase">{{ $certificate->student_name }}</div>
                <p>CPF: {{ $certificate->cpf }} concluiu com aproveitamento o curso de:</p>
                <div class="course">{{ $certificate->course_name }}</div>

                <div class="meta">
                    Emitido em {{ $certificate->issue_date->format('d/m/Y') }}.
                </div>

                @if ($certificate->validation_code)
                <div class="validation-code">Código: {{ $certificate->validation_code }}</div>
                @endif
            </div>

            <div class="signatures">
                <div class="signature-box">
                    <div class="line"></div>
                    @foreach($linesAss1 as $index => $line)
                        @if($index === 0)
                            <div class="sig-name">{{ $line }}</div>
                        @elseif($index === 1)
                            <div class="sig-role">{{ $line }}</div>
                        @else
                            <div class="sig-decree">{{ $line }}</div>
                        @endif
                    @endforeach
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    @foreach($linesAss2 as $index => $line)
                        @if($index === 0)
                            <div class="sig-name">{{ $line }}</div>
                        @elseif($index === 1)
                            <div class="sig-role">{{ $line }}</div>
                        @else
                            <div class="sig-decree">{{ $line }}</div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Second Page (Verso) -->
    <div class="page">
        <div class="certificate-back">
            <div class="back-border">
                <div class="back-left">
                    <div class="back-title">REGISTRO</div>

                    <div class="back-text">
                        Certificado registrado no Núcleo Pedagógico de Capacitação Continuada com
                        autenticidade podendo ser conferida no endereço
                        <a href="https://sei.rr.gov.br/autenticar" target="_blank" class="url">https://sei.rr.gov.br/autenticar</a> informando o verificador 
                        <strong>{{ $verificador }}</strong> e o código CRC <strong>{{ $crc }}</strong>.
                    </div>

                    <div class="back-info-box">
                        <div class="back-info-row">@if($starData)<img src="{{ $starData }}" alt="" style="vertical-align: middle; height: 4.5mm; width: auto; margin-right: 2mm;">@endif {{ mb_strtoupper($certificate->course_name) }}</div>
                        <div class="back-info-row">@if($starData)<img src="{{ $starData }}" alt="" style="vertical-align: middle; height: 4.5mm; width: auto; margin-right: 2mm;">@endif Nome: {{ $certificate->student_name }}</div>
                    </div>

                    <div class="back-date">
                        {{ $issueDateFormatted }}
                    </div>

                    <div class="back-footer">
                        <div class="back-footer-title">Núcleo Pedagógico de Capacitação Continuada - NPCCAP</div>
                        <div class="back-footer-text">
                            Av. Getúlio Vargas, 8120 - São Vicente, Boa Vista - Roraima - CEP 69.303-472 - E-mail: npccaprr@gmail.com<br>
                            Decreto 16.783-E de 17 de março de 2014.
                        </div>
                    </div>
                </div>

                <div class="back-divider"></div>

                <div class="back-right">
                    <div class="back-qr-box">
                        <div class="back-qr-code">
                            {!! $seiQrCodeSvg !!}
                        </div>
                        <div class="back-qr-text">sei.rr.gov.br/sei/controlador_externo</div>
                    </div>

                    @if ($brasaoData)
                    <div class="back-brasao-box">
                        <img src="{{ $brasaoData }}" class="back-brasao-img" alt="Brasão de Roraima">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>

</html>