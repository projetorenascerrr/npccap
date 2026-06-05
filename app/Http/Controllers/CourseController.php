<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('students')
            ->with('students')
            ->orderBy('name')
            ->get();

        return view('courses.index', [
            'courses' => $courses,
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
        ]);

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
        ]);

        $course->update($validated);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Nome do curso atualizado com sucesso.');
    }

    public function show(Course $course)
    {
        $course->load('students');

        return view('courses.show', [
            'course' => $course,
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
