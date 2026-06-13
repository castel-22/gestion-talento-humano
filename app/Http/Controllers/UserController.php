<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SecurityQuestion;
use App\Models\UserSecurityAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'id_desc');
        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'email_asc': $query->orderBy('email', 'asc'); break;
            case 'email_desc': $query->orderBy('email', 'desc'); break;
            default: $query->orderBy('id', 'desc');
        }

        $users = $query->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::all();
        $securityQuestions = SecurityQuestion::all();
        return view('users.create', compact('roles', 'securityQuestions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'security_questions' => 'required|array|size:4',
            'security_questions.*.question_id' => 'required|exists:security_questions,id',
            'security_questions.*.answer' => 'required|string|min:2',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        foreach ($request->security_questions as $item) {
            UserSecurityAnswer::create([
                'user_id' => $user->id,
                'security_question_id' => $item['question_id'],
                'answer' => $item['answer'],
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        $user->load('roles', 'securityAnswers.question');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::all();
        $securityQuestions = SecurityQuestion::all();
        $userAnswers = $user->securityAnswers;
        return view('users.edit', compact('user', 'roles', 'securityQuestions', 'userAnswers'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
            'security_questions' => 'sometimes|array|size:4',
            'security_questions.*.question_id' => 'required_with:security_questions|exists:security_questions,id',
            'security_questions.*.answer' => 'required_with:security_questions|string|min:2',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user->syncRoles([$request->role]);

        if ($request->has('security_questions')) {
            $user->securityAnswers()->delete();
            foreach ($request->security_questions as $item) {
                UserSecurityAnswer::create([
                    'user_id' => $user->id,
                    'security_question_id' => $item['question_id'],
                    'answer' => $item['answer'],
                ]);
            }
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminarte a ti mismo.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function autocomplete(Request $request)
    {
        $term = $request->get('term');
        $users = User::where('name', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);
        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'label' => $user->name . ' (' . $user->email . ')',
                'value' => $user->name,
            ];
        }
        return response()->json($results);
    }
}