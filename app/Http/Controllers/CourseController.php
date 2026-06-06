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
                        ->orWhereHas('students', function ($studentsQuery) use ($search) {
                            $studentsQuery->where('name', 'like', "%{$search}%")
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hours' => ['nullable', 'integer', 'min:1'],
            'course_date' => ['nullable', 'date'],
            'ass1' => ['nullable', 'string', 'max:255'],
            'ass2' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('courses', 'public');
        }

        unset($validated['image']);

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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hours' => ['nullable', 'integer', 'min:1'],
            'course_date' => ['nullable', 'date'],
            'ass1' => ['nullable', 'string', 'max:255'],
            'ass2' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }

            $validated['image_path'] = $request->file('image')->store('courses', 'public');
        }

        unset($validated['image']);

        $course->update($validated);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Curso atualizado com sucesso.');
    }

    public function show(Request $request, Course $course)
    {
        $search = trim((string) $request->query('search', ''));

        $course->load('students');

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

    public function storeStudent(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'regex:/^(\d{11}|\d{3}\.\d{3}\.\d{3}-\d{2})$/'],
        ], [
            'cpf.regex' => 'O CPF deve ter 11 digitos ou estar no formato 000.000.000-00.',
        ]);

        $course->students()->create([
            'name' => $validated['name'],
            'cpf' => $this->normalizeCpf($validated['cpf']),
        ]);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Aluno adicionado ao curso com sucesso.');
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
            'cpf' => ['required', 'string', 'regex:/^(\d{11}|\d{3}\.\d{3}\.\d{3}-\d{2})$/'],
        ], [
            'cpf.regex' => 'O CPF deve ter 11 digitos ou estar no formato 000.000.000-00.',
        ]);

        $student->update([
            'name' => $validated['name'],
            'cpf' => $this->normalizeCpf($validated['cpf']),
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
