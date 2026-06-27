<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $courses = Course::withCount('students')
            ->with('students')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhereHas('students.studentUser', function ($studentUserQuery) use ($search) {
                            $studentUserQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('cpf', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('name')
            ->get();

        return view('courses.index', [
            'courses' => $courses,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'hours'             => ['nullable', 'integer', 'min:1'],
            'course_date'       => ['nullable', 'date'],
            'start_date'        => ['nullable', 'date'],
            'end_date'          => ['nullable', 'date', 'after_or_equal:start_date'],
            'responsible'       => ['nullable', 'string', 'max:255'],
            'minimum_frequency' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'minimum_grade'     => ['nullable', 'numeric', 'min:0', 'max:10'],
            'status'            => ['nullable', 'in:ativo,encerrado,cancelado'],
            'active'            => ['nullable', 'boolean'],
            'ass1'              => ['nullable', 'string', 'max:255'],
            'ass2'              => ['nullable', 'string', 'max:255'],
            'image'             => ['nullable', 'image', 'max:5120'],
            'image_bg'          => ['nullable', 'image', 'max:5120'],
            'verificador'       => ['nullable', 'string', 'max:255'],
            'crc'               => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('courses', 'public');
        }

        unset($validated['image']);

        if ($request->hasFile('image_bg')) {
            $validated['image_bg'] = $request->file('image_bg')->store('backgrounds', 'public');
        } else {
            unset($validated['image_bg']);
        }

        $validated['active'] = $request->boolean('active', true);

        $course = Course::create($validated);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Curso criado com sucesso. Agora adicione os alunos deste curso.');
    }

    public function edit(Course $course)
    {
        return view('courses.edit', [
            'course' => $course,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'hours'             => ['nullable', 'integer', 'min:1'],
            'course_date'       => ['nullable', 'date'],
            'start_date'        => ['nullable', 'date'],
            'end_date'          => ['nullable', 'date', 'after_or_equal:start_date'],
            'responsible'       => ['nullable', 'string', 'max:255'],
            'minimum_frequency' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'minimum_grade'     => ['nullable', 'numeric', 'min:0', 'max:10'],
            'status'            => ['nullable', 'in:ativo,encerrado,cancelado'],
            'active'            => ['nullable', 'boolean'],
            'ass1'              => ['nullable', 'string', 'max:255'],
            'ass2'              => ['nullable', 'string', 'max:255'],
            'image'             => ['nullable', 'image', 'max:5120'],
            'image_bg'          => ['nullable', 'image', 'max:5120'],
            'verificador'       => ['nullable', 'string', 'max:255'],
            'crc'               => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('image')) {
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }

            $validated['image_path'] = $request->file('image')->store('courses', 'public');
        }

        unset($validated['image']);

        if ($request->hasFile('image_bg')) {
            if ($course->image_bg) {
                Storage::disk('public')->delete($course->image_bg);
            }

            $validated['image_bg'] = $request->file('image_bg')->store('backgrounds', 'public');
        } else {
            unset($validated['image_bg']);
        }

        $validated['active'] = $request->boolean('active');

        $course->update($validated);

        return redirect()
            ->route('courses.edit', $course)
            ->with('success', 'Curso atualizado com sucesso.');
    }

    public function show(Request $request, Course $course)
    {
        $search = trim((string) $request->query('search', ''));

        $course->load(['students' => function ($query) {
            $query->with('certificates');
        }]);

        $students = $course->students
            ->when($search !== '', function ($collection) use ($search) {
                return $collection->filter(function ($student) use ($search) {
                    return str_contains(strtolower($student->name), strtolower($search))
                        || str_contains(strtolower($student->cpf), strtolower($search));
                });
            })
            ->values();

        return view('courses.show', [
            'course' => $course,
            'students' => $students,
            'search' => $search,
        ]);
    }

    public function close(Course $course)
    {
        if ($course->status === Course::STATUS_ENCERRADO) {
            return redirect()
                ->route('courses.edit', $course)
                ->with('success', 'Este curso ja esta encerrado.');
        }

        $course->update([
            'status' => Course::STATUS_ENCERRADO,
            'end_date' => $course->end_date ?? now()->toDateString(),
        ]);

        return redirect()
            ->route('courses.edit', $course)
            ->with('success', 'Curso encerrado com sucesso.');
    }

    public function storeStudent(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf'   => ['required', 'string', 'regex:/^(\d{11}|\d{3}\.\d{3}\.\d{3}-\d{2})$/'],
            'email' => ['required', 'email', 'max:255'],
            'frequency' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grade'     => ['nullable', 'numeric', 'min:0', 'max:10'],
            'status'    => ['nullable', 'in:pre-inscrito,inscrito,confirmado,certificado_emitido'],
        ], [
            'cpf.regex' => 'O CPF deve ter 11 digitos ou estar no formato 000.000.000-00.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ter um formato válido.',
        ]);

        $normalizedCpf = $this->normalizeCpf($validated['cpf']);

        // Check if StudentUser already exists with this CPF
        $studentUser = \App\Models\StudentUser::where('cpf', $normalizedCpf)->first();

        if ($studentUser) {
            // Check if the email provided is different and already taken by someone else
            if (strtolower($studentUser->email) !== strtolower($validated['email'])) {
                $emailTaken = \App\Models\StudentUser::where('email', $validated['email'])
                    ->where('cpf', '!=', $normalizedCpf)
                    ->exists();

                if ($emailTaken) {
                    return back()->withErrors(['email' => 'Este e-mail já está sendo utilizado por outro aluno.'])->withInput();
                }

                // Update the existing user's email to match the admin input
                $studentUser->update(['email' => $validated['email']]);
            }
        } else {
            // New user, check if email is already taken
            $emailTaken = \App\Models\StudentUser::where('email', $validated['email'])->exists();

            if ($emailTaken) {
                return back()->withErrors(['email' => 'Este e-mail já está sendo utilizado por outro aluno.'])->withInput();
            }

            // Create StudentUser
            // Password is the numeric digits of CPF
            $rawCpfDigits = preg_replace('/\D/', '', $normalizedCpf);
            \App\Models\StudentUser::create([
                'name' => $validated['name'],
                'cpf' => $normalizedCpf,
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($rawCpfDigits),
            ]);
        }

        $course->students()->create([
            'student_user_id' => $studentUser->id,
            'frequency' => $validated['frequency'] ?? null,
            'grade' => $validated['grade'] ?? null,
            'status' => $validated['status'] ?? \App\Models\Student::STATUS_INSCRITO,
        ]);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Aluno adicionado ao curso e cadastrado como usuário com sucesso.');
    }

    public function showStudent(Course $course, Student $student)
    {
        abort_unless($student->course_id === $course->id, 404);

        return view('courses.show-student', [
            'course' => $course,
            'student' => $student,
        ]);
    }

    public function editStudent(Course $course, Student $student)
    {
        abort_unless($student->course_id === $course->id, 404);

        return view('courses.edit-student', [
            'course' => $course,
            'student' => $student,
        ]);
    }

    public function updateStudent(Request $request, Course $course, Student $student)
    {
        abort_unless($student->course_id === $course->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf'       => ['required', 'string', 'regex:/^(\d{11}|\d{3}\.\d{3}\.\d{3}-\d{2})$/'],
            'email'     => ['nullable', 'email', 'max:255'],
            'frequency' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grade'     => ['nullable', 'numeric', 'min:0', 'max:10'],
            'status'    => ['nullable', 'in:pre-inscrito,inscrito,confirmado,certificado_emitido'],
        ], [
            'cpf.regex' => 'O CPF deve ter 11 digitos ou estar no formato 000.000.000-00.',
        ]);

        $normalizedCpf = $this->normalizeCpf($validated['cpf']);

        // 1. Update corresponding StudentUser
        $studentUser = $student->studentUser;
        if ($studentUser) {
            $oldCpf = $studentUser->cpf;
            $studentUser->update([
                'name' => $validated['name'],
                'cpf' => $normalizedCpf,
                'email' => $validated['email'] ?? $studentUser->email,
            ]);

            // 2. If CPF changed, update existing certificates for integrity
            if ($oldCpf !== $normalizedCpf) {
                \App\Models\Certificate::where('cpf', $oldCpf)->update([
                    'cpf' => $normalizedCpf,
                    'student_name' => $validated['name'],
                ]);
            }
        }

        // 3. Update student enrollment details
        $student->update([
            'frequency' => $validated['frequency'] ?? $student->frequency,
            'grade'     => $validated['grade'] ?? $student->grade,
            'status'    => $validated['status'] ?? $student->status,
        ]);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Aluno atualizado com sucesso.');
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
