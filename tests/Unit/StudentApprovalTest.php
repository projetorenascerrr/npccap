<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_is_approved_if_course_has_no_requirements(): void
    {
        $course = Course::create([
            'name' => 'Course No Req',
            'minimum_frequency' => null,
            'minimum_grade' => null,
            'status' => Course::STATUS_ATIVO,
        ]);

        $studentUser = StudentUser::create([
            'name' => 'Jane Doe',
            'cpf' => '111.111.111-11',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'frequency' => null,
            'grade' => null,
            'status' => Student::STATUS_INSCRITO,
        ]);

        $this->assertTrue($student->isApproved());
    }

    public function test_student_is_approved_if_meets_frequency_and_grade_requirements(): void
    {
        $course = Course::create([
            'name' => 'Course Requirements',
            'minimum_frequency' => 75.00,
            'minimum_grade' => 7.0,
            'status' => Course::STATUS_ATIVO,
        ]);

        $studentUser = StudentUser::create([
            'name' => 'Jane Doe',
            'cpf' => '111.111.111-11',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'frequency' => 80.00,
            'grade' => 8.5,
            'status' => Student::STATUS_INSCRITO,
        ]);

        $this->assertTrue($student->isApproved());
    }

    public function test_student_is_rejected_if_frequency_is_below_minimum(): void
    {
        $course = Course::create([
            'name' => 'Course Requirements',
            'minimum_frequency' => 75.00,
            'minimum_grade' => 5.0,
            'status' => Course::STATUS_ATIVO,
        ]);

        $studentUser = StudentUser::create([
            'name' => 'Jane Doe',
            'cpf' => '111.111.111-11',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'frequency' => 70.00, // below 75
            'grade' => 6.0,
            'status' => Student::STATUS_INSCRITO,
        ]);

        $this->assertFalse($student->isApproved());
    }

    public function test_student_is_rejected_if_grade_is_below_minimum(): void
    {
        $course = Course::create([
            'name' => 'Course Requirements',
            'minimum_frequency' => 75.00,
            'minimum_grade' => 7.0,
            'status' => Course::STATUS_ATIVO,
        ]);

        $studentUser = StudentUser::create([
            'name' => 'Jane Doe',
            'cpf' => '111.111.111-11',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'frequency' => 80.00,
            'grade' => 6.5, // below 7
            'status' => Student::STATUS_INSCRITO,
        ]);

        $this->assertFalse($student->isApproved());
    }

    public function test_certificate_cannot_be_issued_if_course_is_active(): void
    {
        $course = Course::create([
            'name' => 'Active Course',
            'minimum_frequency' => 75.00,
            'minimum_grade' => 7.0,
            'status' => Course::STATUS_ATIVO,
        ]);

        $studentUser = StudentUser::create([
            'name' => 'Jane Doe',
            'cpf' => '111.111.111-11',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'frequency' => 85.00,
            'grade' => 9.0,
            'status' => Student::STATUS_INSCRITO,
        ]);

        $this->assertTrue($student->isApproved());
        $this->assertFalse($student->canIssueCertificate()); // Course is ACTIVE
    }

    public function test_certificate_can_be_issued_if_course_is_closed_and_approved(): void
    {
        $course = Course::create([
            'name' => 'Closed Course',
            'minimum_frequency' => 75.00,
            'minimum_grade' => 7.0,
            'status' => Course::STATUS_ENCERRADO,
        ]);

        $studentUser = StudentUser::create([
            'name' => 'Jane Doe',
            'cpf' => '111.111.111-11',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'frequency' => 85.00,
            'grade' => 9.0,
            'status' => Student::STATUS_INSCRITO,
        ]);

        $this->assertTrue($student->isApproved());
        $this->assertTrue($student->canIssueCertificate()); // Approved and closed
    }
}
