<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\Certificate;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentPortalController extends Controller
{
    public function index()
    {
        $studentUser = Auth::guard('student')->user();
        $cpf = $studentUser->cpf;

        // Fetch all course enrollments for this student (based on CPF)
        $enrollments = Student::where('cpf', $cpf)
            ->with(['course', 'certificates'])
            ->get();

        return view('student.dashboard', [
            'studentUser' => $studentUser,
            'enrollments' => $enrollments,
        ]);
    }

    public function enroll(Course $course)
    {
        $studentUser = Auth::guard('student')->user();

        // Check if already enrolled in this course
        $exists = Student::where('course_id', $course->id)
            ->where('cpf', $studentUser->cpf)
            ->exists();

        if (!$exists) {
            // Check if course is active
            if ($course->status !== Course::STATUS_ATIVO) {
                return redirect()->route('student.dashboard')->with('error', 'Este curso não está ativo para inscrições.');
            }

            Student::create([
                'course_id' => $course->id,
                'name' => $studentUser->name,
                'cpf' => $studentUser->cpf,
                'email' => $studentUser->email,
                'birth_date' => $studentUser->birth_date,
                'status' => Student::STATUS_INSCRITO,
            ]);

            return redirect()->route('student.dashboard')->with('success', 'Inscrição realizada com sucesso no curso: ' . $course->name);
        }

        return redirect()->route('student.dashboard')->with('info', 'Você já está inscrito neste curso.');
    }

    public function downloadCertificate(Certificate $certificate)
    {
        $studentUser = Auth::guard('student')->user();

        // Security check: ensure student owns this certificate
        abort_unless($certificate->cpf === $studentUser->cpf, 403, 'Acesso não autorizado.');

        $signature = Signature::query()->first();
        $certificate->loadMissing('course');

        $backgroundPath = null;
        if ($certificate->course && $certificate->course->image_path) {
            $path = storage_path('app/public/' . $certificate->course->image_path);
            if (file_exists($path)) {
                $type           = pathinfo($path, PATHINFO_EXTENSION);
                $data           = file_get_contents($path);
                $backgroundPath = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $qrCodeSvg = $certificate->qr_code;
        if (! $qrCodeSvg && $certificate->validation_code) {
            $validationUrl = route('certificates.validate', ['code' => $certificate->validation_code]);
            $qrCodeSvg     = QrCode::format('svg')->size(150)->generate($validationUrl);
            $certificate->update(['qr_code' => $qrCodeSvg]);
        }

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate'    => $certificate,
            'signature'      => $signature,
            'backgroundPath' => $backgroundPath,
            'qrCodeSvg'      => $qrCodeSvg,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('certificado-' . $certificate->validation_code . '.pdf');
    }
}
