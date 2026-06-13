<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\SecurityQuestion;
use App\Models\UserSecurityAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        // Cargar roles y respuestas de seguridad con sus preguntas
        $user->load('roles', 'securityAnswers.question');
        $securityQuestions = SecurityQuestion::all(); // Para futuras mejoras

        return view('profile.edit', [
            'user' => $user,
            'securityQuestions' => $securityQuestions,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        ActivityLogger::log('update', 'profile', "El usuario actualizó su información de perfil.");

        return Redirect::route('profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Almacena las preguntas de seguridad a posteriori.
     */
    public function storeSecurityQuestions(Request $request): RedirectResponse
    {
        $request->validate([
            'security_questions' => ['required', 'array', 'size:4'],
            'security_questions.*.question_id' => ['required', 'exists:security_questions,id'],
            'security_questions.*.answer' => ['required', 'string', 'min:2'],
        ]);

        $user = auth()->user();

        // Verificar que no las haya configurado antes
        if ($user->securityAnswers()->count() >= 4) {
            return Redirect::route('profile.edit')->with('error', 'Ya has configurado tus preguntas de seguridad.');
        }

        // Asegurar que las preguntas son únicas
        $questionIds = collect($request->security_questions)->pluck('question_id')->unique();
        if ($questionIds->count() < 4) {
            return Redirect::back()->withInput()->with('error', 'Debes seleccionar 4 preguntas diferentes.');
        }

        foreach ($request->security_questions as $item) {
            UserSecurityAnswer::create([
                'user_id' => $user->id,
                'security_question_id' => $item['question_id'],
                'answer' => $item['answer'], // El modelo debe hashear esto si no está configurado, vamos a verificar.
            ]);
        }

        ActivityLogger::log('update', 'profile', "El usuario configuró sus preguntas de seguridad desde el perfil.");

        return Redirect::route('profile.edit')->with('success', 'Preguntas de seguridad configuradas correctamente.');
    }

}