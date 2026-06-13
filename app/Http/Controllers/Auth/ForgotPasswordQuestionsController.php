<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSecurityAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;

class ForgotPasswordQuestionsController extends Controller
{
    // Mostrar formulario para ingresar email
    public function showVerifyEmailForm()
    {
        return view('auth.forgot-password-questions-email');
    }

    // Verificar que el email existe y redirigir a preguntas
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Guardamos el ID del usuario en sesión para el siguiente paso
        Session::put('password_reset_user_id', $user->id);

        return redirect()->route('password.questions.show', ['user' => $user->id]);
    }

    // Mostrar las preguntas de seguridad del usuario
    public function showQuestions(User $user)
    {
        // Verificar que el ID en sesión coincida con el usuario (seguridad)
        if (Session::get('password_reset_user_id') != $user->id) {
            return redirect()->route('password.questions.email')
                ->withErrors(['email' => 'Sesión inválida. Comienza de nuevo.']);
        }

        $answers = $user->securityAnswers()->with('question')->get();

        if ($answers->count() < 4) {
            return redirect()->route('password.questions.email')
                ->withErrors(['email' => 'El usuario no tiene suficientes preguntas de seguridad.']);
        }

        return view('auth.forgot-password-questions-answers', compact('user', 'answers'));
    }

    // Verificar las respuestas
    public function verifyAnswers(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ]);

        $userId = Session::get('password_reset_user_id');
        if (!$userId) {
            return redirect()->route('password.questions.email')
                ->withErrors(['email' => 'Sesión expirada. Comienza de nuevo.']);
        }

        $user = User::findOrFail($userId);
        $storedAnswers = $user->securityAnswers;

        // Verificar que coincida el número de respuestas
        if (count($request->answers) != $storedAnswers->count()) {
            return back()->withErrors(['answers' => 'Número de respuestas incorrecto.']);
        }

        $valid = true;
        $i = 0;
        foreach ($storedAnswers as $stored) {
            if (!Hash::check($request->answers[$i], $stored->answer)) {
                $valid = false;
                break;
            }
            $i++;
        }

        if (!$valid) {
            return back()->withErrors(['answers' => 'Las respuestas no son correctas.']);
        }

        // Respuestas correctas: permitir cambiar contraseña
        Session::put('password_reset_verified', true);

        return redirect()->route('password.questions.reset');
    }

    // Mostrar formulario para nueva contraseña
    public function showResetForm()
    {
        if (!Session::get('password_reset_verified')) {
            return redirect()->route('password.questions.email')
                ->withErrors(['email' => 'Debes verificar tus preguntas primero.']);
        }

        return view('auth.forgot-password-questions-reset');
    }

    // Actualizar la contraseña
    public function resetPassword(Request $request)
    {
        if (!Session::get('password_reset_verified')) {
            return redirect()->route('password.questions.email')
                ->withErrors(['email' => 'Debes verificar tus preguntas primero.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $userId = Session::get('password_reset_user_id');
        $user = User::findOrFail($userId);
        $user->password = Hash::make($request->password);
        $user->save();

        // Limpiar sesión
        Session::forget(['password_reset_user_id', 'password_reset_verified']);

        return redirect()->route('login')->with('status', 'Contraseña actualizada correctamente. Ahora puedes iniciar sesión.');
    }
}