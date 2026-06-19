<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StudentUser;
use App\Notifications\AdminResetPasswordNotification;
use App\Notifications\StudentResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_admin_can_view_forgot_password_page(): void
    {
        $response = $this->get('/admin/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('RECUPERAÇÃO DE SENHA');
        $response->assertSee('E-mail Corporativo');
    }

    /** @test */
    public function test_admin_receives_reset_password_link_email(): void
    {
        Notification::fake();

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/admin/forgot-password', [
            'email' => 'admin@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Enviamos o link de recuperação de senha para o seu e-mail!');

        Notification::assertSentTo($admin, AdminResetPasswordNotification::class, function ($notification) use ($admin) {
            $this->assertNotEmpty($notification->token);
            return true;
        });
    }

    /** @test */
    public function test_admin_cannot_request_reset_link_with_invalid_email(): void
    {
        $response = $this->post('/admin/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_admin_can_view_reset_password_page(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = Password::broker('users')->createToken($admin);

        $response = $this->get("/admin/reset-password/{$token}?email=admin@example.com");

        $response->assertStatus(200);
        $response->assertSee('REDEFINIÇÃO DE SENHA');
        $response->assertSee('admin@example.com');
    }

    /** @test */
    public function test_admin_can_reset_password_successfully(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('old_password'),
        ]);

        $token = Password::broker('users')->createToken($admin);

        $response = $this->post('/admin/reset-password', [
            'token' => $token,
            'email' => 'admin@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHas('success', 'Sua senha foi redefinida com sucesso!');

        $this->assertTrue(Hash::check('new_password123', $admin->fresh()->password));
    }

    /** @test */
    public function test_student_can_view_forgot_password_page(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('RECUPERAÇÃO DE SENHA');
        $response->assertSee('E-mail Cadastrado');
    }

    /** @test */
    public function test_student_receives_reset_password_link_email(): void
    {
        Notification::fake();

        $student = StudentUser::create([
            'name' => 'Student User',
            'cpf' => '123.456.789-00',
            'email' => 'student@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'student@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Enviamos o link de recuperação de senha para o seu e-mail!');

        Notification::assertSentTo($student, StudentResetPasswordNotification::class, function ($notification) use ($student) {
            $this->assertNotEmpty($notification->token);
            return true;
        });
    }

    /** @test */
    public function test_student_cannot_request_reset_link_with_invalid_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_student_can_view_reset_password_page(): void
    {
        $student = StudentUser::create([
            'name' => 'Student User',
            'cpf' => '123.456.789-00',
            'email' => 'student@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = Password::broker('student_users')->createToken($student);

        $response = $this->get("/reset-password/{$token}?email=student@example.com");

        $response->assertStatus(200);
        $response->assertSee('REDEFINIÇÃO DE SENHA');
        $response->assertSee('student@example.com');
    }

    /** @test */
    public function test_student_can_reset_password_successfully(): void
    {
        $student = StudentUser::create([
            'name' => 'Student User',
            'cpf' => '123.456.789-00',
            'email' => 'student@example.com',
            'password' => bcrypt('old_password'),
        ]);

        $token = Password::broker('student_users')->createToken($student);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'student@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Sua senha foi redefinida com sucesso!');

        $this->assertTrue(Hash::check('new_password123', $student->fresh()->password));
    }
}
