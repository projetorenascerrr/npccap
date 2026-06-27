<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $students = Student::with(['course', 'studentUser'])
            ->select('students.*')
            ->join('student_users', 'students.student_user_id', '=', 'student_users.id')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->whereHas('studentUser', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('cpf', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('course', function ($courseQuery) use ($search) {
                        $courseQuery->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->orderBy('student_users.name')
            ->paginate(20)
            ->withQueryString();

        return view('students.index', [
            'students' => $students,
            'search' => $search,
        ]);
    }
}
