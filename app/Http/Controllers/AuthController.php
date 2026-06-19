<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Credenciais inválidas'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('courses.index'));
    }

    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('courses.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }

    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Não conseguimos encontrar um administrador com esse endereço de e-mail.',
        ]);

        $status = PasswordBroker::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('success', 'Enviamos o link de recuperação de senha para o seu e-mail!')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'email.exists' => 'Não conseguimos encontrar um administrador com esse endereço de e-mail.',
        ]);

        $status = PasswordBroker::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', 'Sua senha foi redefinida com sucesso!')
            : back()->withErrors(['email' => __($status)]);
    }
}
