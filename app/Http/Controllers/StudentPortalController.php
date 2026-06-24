<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\Certificate;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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

    public function editProfile()
    {
        $studentUser = Auth::guard('student')->user();
        return view('student.profile', compact('studentUser'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\StudentUser $studentUser */
        $studentUser = Auth::guard('student')->user();

        // Normalize CPF before validation
        if ($request->has('cpf')) {
            $request->merge([
                'cpf' => $this->normalizeCpf((string) $request->input('cpf')),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'regex:/^(\d{11}|\d{3}\.\d{3}\.\d{3}-\d{2})$/',
                'unique:student_users,cpf,' . $studentUser->id
            ],
            'email' => ['required', 'email', 'max:255', 'unique:student_users,email,' . $studentUser->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'cpf.regex' => 'O CPF deve ter 11 dígitos ou estar no formato 000.000.000-00.',
            'cpf.unique' => 'Este CPF já está cadastrado por outro usuário.',
            'email.unique' => 'Este e-mail já está cadastrado por outro usuário.',
        ]);

        $oldCpf = $studentUser->cpf;
        $newCpf = $validated['cpf'];
        $newName = $validated['name'];
        $newEmail = $validated['email'];

        // Update student user details
        $studentUser->name = $newName;
        $studentUser->cpf = $newCpf;
        $studentUser->email = $newEmail;

        if ($request->filled('password')) {
            $studentUser->password = Hash::make($validated['password']);
        }

        $studentUser->save();

        // Sync updates to students & certificates tables for integrity
        // 1. Update students table
        Student::where('cpf', $oldCpf)->update([
            'cpf' => $newCpf,
            'name' => $newName,
            'email' => $newEmail,
        ]);

        // 2. Update certificates table
        \App\Models\Certificate::where('cpf', $oldCpf)->update([
            'cpf' => $newCpf,
            'student_name' => $newName,
        ]);

        return redirect()->route('student.profile.edit')->with('success', 'Perfil atualizado com sucesso!');
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
