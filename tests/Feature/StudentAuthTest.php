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

        $studentUser = StudentUser::where('cpf', '987.654.321-11')->firstOrFail();

        // Check if student enrollment record was created
        $this->assertDatabaseHas('students', [
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => 'inscrito',
        ]);
    }

    public function test_student_registration_fails_if_cpf_already_exists(): void
    {
        StudentUser::create([
            'name' => 'Existing User',
            'cpf' => '123.456.789-00',
            'email' => 'existing@example.com',
            'birth_date' => '1990-01-01',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'cpf' => '123.456.789-00',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['cpf' => 'Este CPF já está cadastrado.']);
    }

    public function test_student_registration_fails_if_unformatted_cpf_already_exists(): void
    {
        StudentUser::create([
            'name' => 'Existing User',
            'cpf' => '123.456.789-00',
            'email' => 'existing@example.com',
            'birth_date' => '1990-01-01',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'cpf' => '12345678900',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['cpf' => 'Este CPF já está cadastrado.']);
    }

    public function test_student_registration_fails_if_email_already_exists(): void
    {
        StudentUser::create([
            'name' => 'Existing User',
            'cpf' => '123.456.789-00',
            'email' => 'existing@example.com',
            'birth_date' => '1990-01-01',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'cpf' => '987.654.321-11',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'Este e-mail já está cadastrado.']);
    }

    public function test_authenticated_student_can_view_profile_edit_page(): void
    {
        $studentUser = StudentUser::create([
            'name' => 'John Doe',
            'cpf' => '123.456.789-00',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($studentUser, 'student')->get('/student/profile');
        $response->assertStatus(200);
        $response->assertSee('Editar Minhas Informações');
    }

    public function test_student_can_update_profile_successfully(): void
    {
        $studentUser = StudentUser::create([
            'name' => 'John Doe',
            'cpf' => '123.456.789-00',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($studentUser, 'student')
            ->put('/student/profile', [
                'name' => 'John Updated',
                'cpf' => '123.456.789-00',
                'email' => 'john.updated@example.com',
            ]);

        $response->assertRedirect('/student/profile');
        $response->assertSessionHas('success', 'Perfil atualizado com sucesso!');

        $this->assertDatabaseHas('student_users', [
            'id' => $studentUser->id,
            'name' => 'John Updated',
            'email' => 'john.updated@example.com',
        ]);
    }

    public function test_student_profile_update_syncs_to_enrollments_and_certificates(): void
    {
        $studentUser = StudentUser::create([
            'name' => 'John Doe',
            'cpf' => '123.456.789-00',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $course = Course::create([
            'name' => 'Curso de Teste',
            'hours' => 40,
            'status' => 'ativo',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => 'inscrito',
        ]);

        $certificate = \App\Models\Certificate::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'student_name' => 'John Doe',
            'cpf' => '123.456.789-00',
            'course_name' => 'Curso de Teste',
            'issue_date' => '2026-06-18',
            'validation_code' => 'CERT-2026-000001',
        ]);

        // Update profile, changing CPF, name and email
        $response = $this->actingAs($studentUser, 'student')
            ->put('/student/profile', [
                'name' => 'John Changed',
                'cpf' => '987.654.321-99', // Change CPF
                'email' => 'john.changed@example.com',
            ]);

        $response->assertRedirect('/student/profile');

        // Check if student user updated
        $this->assertDatabaseHas('student_users', [
            'id' => $studentUser->id,
            'name' => 'John Changed',
            'cpf' => '987.654.321-99',
            'email' => 'john.changed@example.com',
        ]);

        // Check if student enrollment has correct relationship and dynamically returns correct name/cpf/email
        $student->refresh();
        $this->assertEquals('John Changed', $student->name);
        $this->assertEquals('987.654.321-99', $student->cpf);
        $this->assertEquals('john.changed@example.com', $student->email);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'student_user_id' => $studentUser->id,
        ]);

        // Check if certificate updated
        $this->assertDatabaseHas('certificates', [
            'id' => $certificate->id,
            'student_name' => 'John Changed',
            'cpf' => '987.654.321-99',
        ]);
    }

    public function test_student_profile_update_fails_with_duplicate_cpf_or_email(): void
    {
        $studentUserA = StudentUser::create([
            'name' => 'User A',
            'cpf' => '123.456.789-00',
            'email' => 'usera@example.com',
            'password' => bcrypt('password123'),
        ]);

        $studentUserB = StudentUser::create([
            'name' => 'User B',
            'cpf' => '987.654.321-11',
            'email' => 'userb@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Try updating User A's CPF to User B's CPF
        $response = $this->actingAs($studentUserA, 'student')
            ->put('/student/profile', [
                'name' => 'User A Updated',
                'cpf' => '98765432111', // Unformatted but duplicate
                'email' => 'usera@example.com',
            ]);
        $response->assertSessionHasErrors(['cpf']);

        // Try updating User A's email to User B's email
        $response = $this->actingAs($studentUserA, 'student')
            ->put('/student/profile', [
                'name' => 'User A Updated',
                'cpf' => '123.456.789-00',
                'email' => 'userb@example.com',
            ]);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_adding_student_creates_student_user_successfully(): void
    {
        // 1. Create a course
        $course = Course::create([
            'name' => 'Curso Admin Test',
            'hours' => 30,
            'status' => 'ativo',
        ]);

        // 2. Create an admin user
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 3. Make POST to courses.students.store
        $response = $this->actingAs($admin, 'web')
            ->post("/admin/courses/{$course->id}/students", [
                'name' => 'Bob Builder',
                'cpf' => '555.555.555-55',
                'email' => 'bob@example.com',
                'status' => 'inscrito',
            ]);

        // 4. Assert redirect back to course page
        $response->assertRedirect("/admin/courses/{$course->id}");

        // 5. Assert database has student user with CPF as password
        $this->assertDatabaseHas('student_users', [
            'name' => 'Bob Builder',
            'cpf' => '555.555.555-55',
            'email' => 'bob@example.com',
        ]);

        $studentUser = StudentUser::where('cpf', '555.555.555-55')->firstOrFail();

        // 6. Assert student enrollment in course
        $this->assertDatabaseHas('students', [
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
        ]);

        // 7. Verify login using password (which should be numeric digits of CPF: '55555555555')
        $loginResponse = $this->post('/login', [
            'cpf' => '555.555.555-55',
            'password' => '55555555555',
        ]);
        $loginResponse->assertRedirect('/student/dashboard');
    }

    public function test_course_verificador_and_crc_flow(): void
    {
        // 1. Create admin user
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin_test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. Post to create a course with verificador and crc
        $response = $this->actingAs($admin, 'web')
            ->post('/admin/courses', [
                'name' => 'Course V-CRC Test',
                'description' => 'Test course description',
                'hours' => 20,
                'status' => 'ativo',
                'verificador' => '12345678',
                'crc' => 'ABCDEF12',
            ]);

        $course = Course::where('name', 'Course V-CRC Test')->firstOrFail();
        $this->assertEquals('12345678', $course->verificador);
        $this->assertEquals('ABCDEF12', $course->crc);

        // 3. Put to update the course verificador and crc
        $response = $this->actingAs($admin, 'web')
            ->put("/admin/courses/{$course->id}", [
                'name' => 'Course V-CRC Test Updated',
                'description' => 'Test course description',
                'hours' => 20,
                'status' => 'ativo',
                'verificador' => '87654321',
                'crc' => 'FEDCBA98',
            ]);

        $course->refresh();
        $this->assertEquals('87654321', $course->verificador);
        $this->assertEquals('FEDCBA98', $course->crc);

        $studentUser = StudentUser::create([
            'name' => 'Test Student',
            'cpf' => '000.111.222-33',
            'email' => 'student_test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 4. Create student and certificate to test PDF rendering view variables
        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => 'confirmado',
        ]);

        $certificate = \App\Models\Certificate::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'cpf' => $student->cpf,
            'course_name' => $course->name,
            'issue_date' => now(),
            'validation_code' => 'CERT-2026-999999',
        ]);

        // Access the PDF rendering view and assert variables are populated from the course
        $pdfView = view('certificates.pdf', [
            'certificate' => $certificate,
            'signature' => null,
            'backgroundPath' => null,
            'qrCodeSvg' => '',
        ])->render();

        $this->assertStringContainsString('87654321', $pdfView);
        $this->assertStringContainsString('FEDCBA98', $pdfView);
    }

    public function test_admin_can_access_students_list_page_and_search()
    {
        $admin = \App\Models\User::factory()->create();

        $studentUser = StudentUser::create([
            'name' => 'Aluno Teste Especial',
            'cpf' => '123.456.789-01',
            'email' => 'teste.especial@exemplo.com',
            'password' => bcrypt('password123'),
        ]);

        $course = Course::create([
            'name' => 'Curso de Teste',
            'status' => 'ativo',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => 'inscrito',
        ]);

        $this->actingAs($admin, 'web');

        // 1. Access main students list page
        $response = $this->get('/admin/students');
        $response->assertStatus(200);
        $response->assertSee('Aluno Teste Especial');

        // 2. Search for the student
        $responseSearch = $this->get('/admin/students?search=Especial');
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Aluno Teste Especial');
    }

    public function test_admin_can_access_edit_student_page_and_update()
    {
        $admin = \App\Models\User::factory()->create();

        $studentUser = StudentUser::create([
            'name' => 'Aluno Edicao',
            'cpf' => '123.456.789-02',
            'email' => 'edicao@exemplo.com',
            'password' => bcrypt('password123'),
        ]);

        $course = Course::create([
            'name' => 'Curso de Teste',
            'status' => 'ativo',
        ]);

        $student = Student::create([
            'course_id' => $course->id,
            'student_user_id' => $studentUser->id,
            'status' => 'inscrito',
        ]);

        $this->actingAs($admin, 'web');

        // 1. GET edit page
        $response = $this->get("/admin/courses/{$course->id}/students/{$student->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Editar aluno');

        // 2. PUT update student
        $responseUpdate = $this->put("/admin/courses/{$course->id}/students/{$student->id}", [
            'name' => 'Aluno Editado Nome',
            'cpf' => '123.456.789-02',
            'email' => 'editado@exemplo.com',
            'frequency' => 85.5,
            'grade' => 7.5,
            'status' => 'confirmado',
        ]);

        $responseUpdate->assertRedirect(route('courses.show', $course));

        // Assert updated in Database
        $this->assertDatabaseHas('student_users', [
            'id' => $studentUser->id,
            'name' => 'Aluno Editado Nome',
            'email' => 'editado@exemplo.com',
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'frequency' => 85.5,
            'grade' => 7.5,
            'status' => 'confirmado',
        ]);
    }
}


