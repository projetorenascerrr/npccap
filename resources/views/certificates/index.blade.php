<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Certificados - NPCCAP</title>
    <style>
        :root {
            --bg: #f6f1e8;
            --surface: #ffffff;
            --ink: #1e2a32;
            --accent: #ad7a2f;
            --accent-soft: #e8d5b6;
            --line: #d8c5a4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% 10%, #fff7e6 0%, transparent 45%),
                radial-gradient(circle at 90% 90%, #f4e6cc 0%, transparent 40%),
                var(--bg);
            min-height: 100vh;
            padding: 24px;
        }

        .wrapper {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            gap: 20px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(30, 42, 50, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            letter-spacing: 0.04em;
        }

        p {
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            align-items: end;
        }

        label {
            display: block;
            font-size: 0.92rem;
            margin-bottom: 6px;
            font-weight: 700;
        }

        input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 1rem;
        }

        button {
            border: 0;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-weight: 700;
            padding: 11px 16px;
            cursor: pointer;
        }

        .alert {
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 14px;
        }

        .alert-success {
            background: #ebf8ee;
            border: 1px solid #96d4a2;
        }

        .alert-error {
            background: #fff1f1;
            border: 1px solid #e4a8a8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        th,
        td {
            text-align: left;
            border-bottom: 1px solid var(--line);
            padding: 10px 8px;
        }

        th {
            background: var(--accent-soft);
        }

        .btn-link {
            text-decoration: none;
            background: #2f5c8f;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
            font-size: 0.85rem;
        }

        @media (max-width: 700px) {
            body {
                padding: 12px;
            }

            .card {
                padding: 16px;
            }

            table,
            thead,
            tbody,
            tr,
            td,
            th {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                border: 1px solid var(--line);
                border-radius: 10px;
                margin-bottom: 12px;
                padding: 8px;
            }

            td {
                border: 0;
                padding: 6px 0;
            }
        }

    </style>
</head>
<body>
    <div class="wrapper">
        <section class="card">
            <h1>NPCCAP - Gerador de Certificados</h1>
            <p>Cadastre curso, aluno e CPF para gerar um certificado A4 horizontal em PDF.</p>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-error">
                <strong>Erros encontrados:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('certificates.store') }}">
                @csrf
                <div class="form-grid">
                    <div>
                        <label for="student_name">Nome do Aluno</label>
                        <input id="student_name" name="student_name" value="{{ old('student_name') }}" required>
                    </div>

                    <div>
                        <label for="cpf">CPF</label>
                        <input id="cpf" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00" required>
                    </div>

                    <div>
                        <label for="course_name">Curso</label>
                        <input id="course_name" name="course_name" value="{{ old('course_name') }}" required>
                    </div>

                    <div>
                        <label for="issue_date">Data de Emissao</label>
                        <input id="issue_date" name="issue_date" type="date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                    </div>

                    <div>
                        <button type="submit">Salvar Certificado</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Certificados Cadastrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Aluno</th>
                        <th>CPF</th>
                        <th>Curso</th>
                        <th>Data</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($certificates as $certificate)
                    <tr>
                        <td>{{ $certificate->id }}</td>
                        <td>{{ $certificate->student_name }}</td>
                        <td>{{ $certificate->cpf }}</td>
                        <td>{{ $certificate->course_name }}</td>
                        <td>{{ $certificate->issue_date->format('d/m/Y') }}</td>
                        <td>
                            <a class="btn-link" href="{{ route('certificates.pdf', $certificate) }}" target="_blank">Gerar PDF</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">Nenhum certificado cadastrado ainda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
