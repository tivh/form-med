<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $actingUser = $request->user();

        $query = User::query()->latest();

        if (!$actingUser->isSuperAdmin()) {
            $query->where('form_scope', $actingUser->form_scope);
        }

        $users = $query->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
            'isSuperAdmin' => $actingUser->isSuperAdmin(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.users.create', [
            'isSuperAdmin' => $request->user()->isSuperAdmin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $actingUser = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($actingUser->isSuperAdmin()) {
            $rules['form_scope'] = ['nullable', 'string', 'in:form-med,regime-tributario'];
        }

        $data = $request->validate($rules);

        // Admin escopado só pode criar usuário dentro do próprio escopo;
        // só o Super Admin escolhe a área livremente.
        // dentro de store(), troque a linha do form_scope por:
        if ($actingUser->isSuperAdmin()) {
            $rules['form_scope'] = ['nullable', 'string', 'in:' . implode(',', array_keys(config('admin_areas')))];
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'form_scope' => $formScope,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário criado com sucesso.');
    }
}