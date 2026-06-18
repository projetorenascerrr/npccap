<?php

namespace App\Http\Controllers;

use App\Models\StudentUser;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StudentAuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        return view('student.auth.login', [
            'course_id' => $request->query('course_id'),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'cpf' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $normalizedCpf = $this->normalizeCpf($credentials['cpf']);

        if (! Auth::guard('student')->attempt([
            'cpf' => $normalizedCpf,
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['cpf' => 'Credenciais inválidas ou CPF não cadastrado.'])
                ->onlyInput('cpf');
        }

        $request->session()->regenerate();

        // Check if we need to auto-enroll in a course
        if ($request->filled('course_id')) {
            $courseId = $request->input('course_id');
            $this->enrollStudentInCourse(Auth::guard('student')->user(), $courseId);
            return redirect()->route('student.dashboard')->with('success', 'Login realizado e inscrição efetuada com sucesso!');
        }

        return redirect()->intended(route('student.dashboard'));
    }

    public function showRegisterForm(Request $request)
    {
        return view('student.auth.register', [
            'course_id' => $request->query('course_id'),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'regex:/^(\d{11}|\d{3}\.\d{3}\.\d{3}-\d{2})$/', 'unique:student_users,cpf'],
            'email' => ['required', 'email', 'max:255', 'unique:student_users,email'],
            'birth_date' => ['required', 'date'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'cpf.regex' => 'O CPF deve ter 11 dígitos ou estar no formato 000.000.000-00.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.unique' => 'Este e-mail já está cadastrado.',
        ]);

        $normalizedCpf = $this->normalizeCpf($validated['cpf']);

        $studentUser = StudentUser::create([
            'name' => $validated['name'],
            'cpf' => $normalizedCpf,
            'email' => $validated['email'],
            'birth_date' => $validated['birth_date'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('student')->login($studentUser);

        // Check if we need to auto-enroll in a course
        if ($request->filled('course_id')) {
            $courseId = $request->input('course_id');
            $this->enrollStudentInCourse($studentUser, $courseId);
            return redirect()->route('student.dashboard')->with('success', 'Cadastro realizado e inscrição efetuada com sucesso!');
        }

        return redirect()->route('student.dashboard')->with('success', 'Cadastro realizado com sucesso!');
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }

    private function enrollStudentInCourse(StudentUser $user, $courseId)
    {
        $course = Course::find($courseId);
        if ($course) {
            // Check if enrollment already exists in the students table
            $exists = Student::where('course_id', $courseId)
                ->where('cpf', $user->cpf)
                ->exists();

            if (!$exists) {
                Student::create([
                    'course_id' => $course->id,
                    'name' => $user->name,
                    'cpf' => $user->cpf,
                    'email' => $user->email,
                    'birth_date' => $user->birth_date,
                    'status' => Student::STATUS_INSCRITO,
                ]);
            }
        }
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
