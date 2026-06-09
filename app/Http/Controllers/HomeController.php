<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Signature;
use App\Models\Student;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'courses_total' => Course::query()->count(),
            'courses_active' => Course::query()->where('status', Course::STATUS_ATIVO)->count(),
            'students_total' => Student::query()->count(),
            'certificates_total' => Certificate::query()->count(),
            'signatures_configured' => Signature::query()->exists(),
        ];

        $recentCourses = Course::query()
            ->withCount('students')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentCertificates = Certificate::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('home', [
            'stats' => $stats,
            'recentCourses' => $recentCourses,
            'recentCertificates' => $recentCertificates,
        ]);
    }
}
