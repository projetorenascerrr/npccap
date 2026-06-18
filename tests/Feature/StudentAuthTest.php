<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('PORTAL DO ALUNO');
    }

    public function test_student_registration_page_renders_successfully(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('PORTAL DO ALUNO');
    }

    public function test_guest_is_redirected_to_admin_login_when_accessing_admin_routes(): void
    {
        $response = $this->get('/admin/home');
        $response->assertRedirect('/admin/login');
    }

    public function test_student_can_register_successfully(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'cpf' => '123.456.789-00',
            'email' => 'john@example.com',
            'birth_date' => '2000-01-01',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertDatabaseHas('student_users', [
            'email' => 'john@example.com',
            'cpf' => '123.456.789-00',
        ]);
    }

    public function test_student_can_register_and_auto_enroll_in_course(): void
    {
        $course = Course::create([
            'name' => 'Curso de Teste',
            'hours' => 40,
            'status' => 'ativo',
        ]);

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'cpf' => '987.654.321-11',
            'email' => 'jane@example.com',
            'birth_date' => '1995-05-15',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'course_id' => $course->id,
        ]);

        $response->assertRedirect('/student/dashboard');

        // Check if student user was created
        $this->assertDatabaseHas('student_users', [
            'email' => 'jane@example.com',
            'cpf' => '987.654.321-11',
        ]);

        // Check if student enrollment record was created
        $this->assertDatabaseHas('students', [
            'course_id' => $course->id,
            'cpf' => '987.654.321-11',
            'status' => 'inscrito',
        ]);
    }
}
