<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSecurityAnswer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use Spatie\Permission\Models\Role; // Para asignar rol

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required', 'captcha'],
            'security_questions' => ['required', 'array', 'size:4'],
            'security_questions.*.question_id' => ['required', 'exists:security_questions,id'],
            'security_questions.*.answer' => ['required', 'string', 'min:2'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar rol por defecto (secretaria)
        $user->assignRole('secretaria');

        // Guardar respuestas de seguridad
        foreach ($request->security_questions as $item) {
            try {
                UserSecurityAnswer::create([
                    'user_id' => $user->id,
                    'security_question_id' => $item['question_id'],
                    'answer' => $item['answer'], // El mutador lo hasheará
                ]);
            } catch (\Exception $e) {
                // Si hay error, lo mostramos para depurar
                dd('Error al guardar respuesta:', $e->getMessage(), $item);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}