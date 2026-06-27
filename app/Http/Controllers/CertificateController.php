<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateLog;
use App\Models\Course;
use App\Models\Signature;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $certificates = Certificate::with(['course', 'student'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('course_name', 'like', "%{$search}%")
                        ->orWhere('student_name', 'like', "%{$search}%")
                        ->orWhere('cpf', 'like', "%{$search}%")
                        ->orWhere('validation_code', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        $signature = Signature::query()->first();

        $courses = Course::with('students')
            ->orderBy('name')
            ->get();

        $coursesForJs = $courses->map(function (Course $course) {
            return [
                'id'       => $course->id,
                'name'     => $course->name,
                'status'   => $course->status,
                'students' => $course->students->map(function (Student $student) use ($course) {
                    $student->setRelation('course', $course);
                    return [
                        'id'                   => $student->id,
                        'name'                 => $student->name,
                        'cpf'                  => $student->cpf,
                        'can_issue_certificate' => $student->canIssueCertificate(),
                        'is_approved'           => $student->isApproved(),
                        'status'               => $student->status,
                    ];
                })->values(),
            ];
        })->values();

        return view('certificates.index', [
            'certificates' => $certificates,
            'courses'      => $courses,
            'coursesForJs' => $coursesForJs,
            'signature'    => $signature,
            'search'       => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'  => ['required', 'exists:courses,id'],
            'student_id' => ['required', 'exists:students,id'],
            'issue_date' => ['required', 'date'],
        ], [
            'course_id.required'  => 'Selecione um curso.',
            'student_id.required' => 'Selecione um aluno.',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        $student = Student::whereKey($validated['student_id'])
            ->where('course_id', $course->id)
            ->with('course')
            ->firstOrFail();

        // RN011 – Elegibilidade
        if (! $student->canIssueCertificate()) {
            $reason = 'O aluno não está apto para emissão no momento.';
            if (!$course->isEncerrado()) {
                $reason = 'O curso deve estar encerrado para que certificados possam ser emitidos.';
            } elseif (!$student->isApproved()) {
                $reason = 'O aluno não atingiu os critérios mínimos de aprovação (nota/frequência).';
            }

            return back()->withErrors(['student_id' => $reason])->withInput();
        }

        // RN012 – Código único de validação
        $validationCode = Certificate::generateValidationCode();

        // RN013 – QR Code aponta para página pública de validação
        $validationUrl = route('certificates.validate', ['code' => $validationCode]);
        $qrCodeSvg     = QrCode::format('svg')->size(150)->generate($validationUrl);

        $certificate = Certificate::create([
            'course_id'       => $course->id,
            'student_id'      => $student->id,
            'student_name'    => $student->name,
            'cpf'             => $student->cpf,
            'course_name'     => $course->name,
            'issue_date'      => $validated['issue_date'],
            'validation_code' => $validationCode,
            'qr_code'         => $qrCodeSvg,
        ]);

        // RN017 – Registra log de emissão
        $certificate->logs()->create([
            'action'       => CertificateLog::ACTION_ISSUED,
            'notes'        => 'Certificado emitido.',
            'performed_by' => 'system',
            'performed_at' => now(),
        ]);

        // Atualiza status do aluno (RN003)
        $student->update(['status' => Student::STATUS_CERTIFICADO]);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Certificado emitido com sucesso. Código: ' . $validationCode);
    }

    // RN015 – Segunda via
    public function reissue(Certificate $certificate)
    {
        $certificate->reissue('system');

        return redirect()
            ->route('certificates.pdf', $certificate)
            ->with('success', 'Segunda via gerada. O código de validação permanece o mesmo.');
    }

    public function show()
    {
        $signature = Signature::query()->first();

        $courses = Course::with('students')
            ->orderBy('name')
            ->get();

        $coursesForJs = $courses->map(function (Course $course) {
            return [
                'id'        => $course->id,
                'name'      => $course->name,
                'image_url' => $course->image_path ? asset('storage/' . $course->image_bg) : null,
                'students'  => $course->students->map(function (Student $student) {
                    return [
                        'id'   => $student->id,
                        'name' => $student->name,
                        'cpf'  => $student->cpf,
                    ];
                })->values(),
            ];
        })->values();

        return view('certificates.show', [
            'signature'    => $signature,
            'courses'      => $courses,
            'coursesForJs' => $coursesForJs,
        ]);
    }

    public function pdf(Certificate $certificate)
    {
        $signature = Signature::query()->first();
        $certificate->loadMissing('course');

        $backgroundPath = null;
        if ($certificate->course && $certificate->course->image_bg) {
            $path = storage_path('app/public/' . $certificate->course->image_bg);
            if (file_exists($path)) {
                $type           = pathinfo($path, PATHINFO_EXTENSION);
                $data           = file_get_contents($path);
                $backgroundPath = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        // RN013 – QR Code no PDF (regenera se necessário e passa como SVG Base64 para melhor compatibilidade com DomPDF sem requerer Imagick)
        $validationUrl = route('certificates.validate', ['code' => $certificate->validation_code]);
        $qrCodeSvgXml  = QrCode::format('svg')->size(150)->generate($validationUrl);
        $qrCodeBase64  = 'data:image/svg+xml;base64,' . base64_encode((string)$qrCodeSvgXml);

        if (! $certificate->qr_code && $certificate->validation_code) {
            $certificate->update(['qr_code' => $qrCodeSvgXml]);
        }

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate'    => $certificate,
            'signature'      => $signature,
            'backgroundPath' => $backgroundPath,
            'qrCodeSvg'      => $qrCodeBase64,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('certificado-' . $certificate->validation_code . '.pdf');
    }

    // RN014 – Página pública de validação via QR Code
    public function validateCertificate(string $code)
    {
        $certificate = Certificate::where('validation_code', $code)
            ->with('course')
            ->firstOrFail();

        return view('certificates.public-validate', [
            'certificate' => $certificate,
        ]);
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
