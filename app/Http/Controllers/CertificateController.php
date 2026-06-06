<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Certificate;
use App\Models\Signature;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
                'id' => $course->id,
                'name' => $course->name,
                'students' => $course->students->map(function (Student $student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'cpf' => $student->cpf,
                    ];
                })->values(),
            ];
        })->values();

        return view('certificates.index', [
            'certificates' => $certificates,
            'courses' => $courses,
            'coursesForJs' => $coursesForJs,
            'signature' => $signature,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'student_id' => ['required', 'exists:students,id'],
            'issue_date' => ['required', 'date'],
        ], [
            'course_id.required' => 'Selecione um curso.',
            'student_id.required' => 'Selecione um aluno.',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $student = Student::whereKey($validated['student_id'])
            ->where('course_id', $course->id)
            ->firstOrFail();

        $certificate = Certificate::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'cpf' => $student->cpf,
            'course_name' => $course->name,
            'issue_date' => $validated['issue_date'],
        ]);

        return redirect()
            ->route('certificates.index')
            ->with('success', 'Certificado cadastrado com sucesso.');
    }

    public function show()
    {
        $signature = Signature::query()->first();

        $courses = Course::with('students')
            ->orderBy('name')
            ->get();

        $coursesForJs = $courses->map(function (Course $course) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'image_url' => $course->image_path ? asset('storage/' . $course->image_path) : null,
                'students' => $course->students->map(function (Student $student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'cpf' => $student->cpf,
                    ];
                })->values(),
            ];
        })->values();

        return view('certificates.show', [
            'signature' => $signature,
            'courses' => $courses,
            'coursesForJs' => $coursesForJs,
        ]);
    }

    public function pdf(Certificate $certificate)
    {
        $signature = Signature::query()->first();

        $certificate->loadMissing('course');

        $backgroundPath = null;
        if ($certificate->course && $certificate->course->image_path) {
            $path = storage_path('app/public/' . $certificate->course->image_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $backgroundPath = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'signature' => $signature,
            'backgroundPath' => $backgroundPath,
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
