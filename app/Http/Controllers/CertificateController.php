<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::query()
            ->latest()
            ->get();

        return view('certificates.index', [
            'certificates' => $certificates,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'regex:/^(\d{11}|\d{3}\.\d{3}\.\d{3}-\d{2})$/'],
            'course_name' => ['required', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
        ], [
            'cpf.regex' => 'O CPF deve ter 11 digitos ou estar no formato 000.000.000-00.',
        ]);

        $validated['cpf'] = $this->normalizeCpf($validated['cpf']);

        $certificate = Certificate::create($validated);

        return redirect()
            ->route('certificates.index')
            ->with('success', 'Certificado cadastrado com sucesso.');
    }

    public function pdf(Certificate $certificate)
    {
        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('certificado-' . $certificate->id . '.pdf');
    }

    private function normalizeCpf(string $cpf): string
    {
        $onlyNumbers = preg_replace('/\D/', '', $cpf) ?? '';

        if (strlen($onlyNumbers) !== 11) {
            return $cpf;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $onlyNumbers) ?? $cpf;
    }
}
