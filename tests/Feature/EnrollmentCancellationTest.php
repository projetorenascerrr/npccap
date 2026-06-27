<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentUser;
use App\Models\User;
use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentCancellationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_student_can_cancel_their_own_active_enrollment()
    {
        $studentUser = StudentUser::create([
            'name' => 'Aluno Teste',
            'cpf' => '111.111.111-11',
            'email' => 'aluno@teste.com',
            'password' => bcrypt('password')
        ]);

        $course = Course::create([
            'name' => 'Curso de Laravel',
            'status' => Course::STATUS_ATIVO
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => Student::STATUS_INSCRITO,
            'frequency' => 50,
            'grade' => 6.0
        ]);

        $this->actingAs($studentUser, 'student');

        $response = $this->delete(route('student.enrollment.cancel', $student));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('success', 'Inscrição cancelada com sucesso!');
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    /** @test */
    public function a_student_cannot_cancel_another_students_enrollment()
    {
        $studentUser1 = StudentUser::create([
            'name' => 'Aluno 1',
            'cpf' => '111.111.111-11',
            'email' => 'aluno1@teste.com',
            'password' => bcrypt('password')
        ]);

        $studentUser2 = StudentUser::create([
            'name' => 'Aluno 2',
            'cpf' => '222.222.222-22',
            'email' => 'aluno2@teste.com',
            'password' => bcrypt('password')
        ]);

        $course = Course::create([
            'name' => 'Curso Teste',
            'status' => Course::STATUS_ATIVO
        ]);

        $studentOfUser2 = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser2->id,
            'status' => Student::STATUS_INSCRITO
        ]);

        $this->actingAs($studentUser1, 'student');

        $response = $this->delete(route('student.enrollment.cancel', $studentOfUser2));

        $response->assertStatus(403);
        $this->assertDatabaseHas('students', ['id' => $studentOfUser2->id]);
    }

    /** @test */
    public function a_student_cannot_cancel_enrollment_if_already_completed_or_has_certificate()
    {
        $studentUser = StudentUser::create([
            'name' => 'Aluno Teste',
            'cpf' => '111.111.111-11',
            'email' => 'aluno@teste.com',
            'password' => bcrypt('password')
        ]);

        $course = Course::create([
            'name' => 'Curso Teste',
            'status' => Course::STATUS_ATIVO
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => Student::STATUS_CERTIFICADO // concluído/certificado_emitido
        ]);

        $this->actingAs($studentUser, 'student');

        $response = $this->delete(route('student.enrollment.cancel', $student));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Não é possível cancelar inscrição de curso concluído ou com certificado emitido.');
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    /** @test */
    public function an_admin_can_remove_a_student_from_a_course()
    {
        $admin = User::factory()->create();
        
        $studentUser = StudentUser::create([
            'name' => 'Aluno Teste',
            'cpf' => '111.111.111-11',
            'email' => 'aluno@teste.com',
            'password' => bcrypt('password')
        ]);

        $course = Course::create([
            'name' => 'Curso Teste',
            'status' => Course::STATUS_ATIVO
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => Student::STATUS_INSCRITO
        ]);

        $this->actingAs($admin, 'web');

        $response = $this->delete(route('courses.students.destroy', [$course, $student]));

        $response->assertRedirect(route('courses.show', $course));
        $response->assertSessionHas('success', 'Aluno removido do curso com sucesso.');
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    /** @test */
    public function an_admin_cannot_remove_a_student_if_they_have_an_associated_certificate()
    {
        $admin = User::factory()->create();
        
        $studentUser = StudentUser::create([
            'name' => 'Aluno Teste',
            'cpf' => '111.111.111-11',
            'email' => 'aluno@teste.com',
            'password' => bcrypt('password')
        ]);

        $course = Course::create([
            'name' => 'Curso Teste',
            'status' => Course::STATUS_ATIVO
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => Student::STATUS_CERTIFICADO
        ]);

        // Create associated certificate
        $certificate = Certificate::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'student_name' => $studentUser->name,
            'cpf' => $studentUser->cpf,
            'course_name' => $course->name,
            'issue_date' => now()->format('Y-m-d'),
            'validation_code' => 'CERT-TEST'
        ]);

        $this->actingAs($admin, 'web');

        $response = $this->delete(route('courses.students.destroy', [$course, $student]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Não é possível remover o aluno pois ele possui certificados associados.');
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }
}
