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
            $rules['form_scope'] = ['nullable', 'string', 'in:' . implode(',', array_keys(config('admin_areas')))] ;
            $rules['admin_role'] = ['nullable', 'array'];
            $rules['admin_role.*'] = ['string', 'in:pj_diverso,pj_colaborador,pf'];
        }

        $data = $request->validate($rules);

        // Admin escopado só cria usuário dentro do próprio escopo, não escolhe.
        $formScope = $actingUser->isSuperAdmin()
            ? ($data['form_scope'] ?? null)
            : $actingUser->form_scope;

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'form_scope' => $formScope,
            'admin_role' => $this->normalizeAdminRole($data['admin_role'] ?? null),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário criado com sucesso.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeManage($request->user(), $user);

        return view('admin.users.edit', [
            'user' => $user,
            'isSuperAdmin' => $request->user()->isSuperAdmin(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $actingUser = $request->user();

        $this->authorizeManage($actingUser, $user);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($actingUser->isSuperAdmin()) {
            $rules['form_scope'] = ['nullable', 'string', 'in:' . implode(',', array_keys(config('admin_areas')))];            $rules['admin_role'] = ['nullable', 'array'];
            $rules['admin_role.*'] = ['string', 'in:pj_diverso,pj_colaborador,pf'];        }

        $data = $request->validate($rules);

        // Impede o último Super Admin de se rebaixar e travar o próprio acesso.
        if (
            $actingUser->isSuperAdmin()
            && $user->id === $actingUser->id
            && array_key_exists('form_scope', $data)
            && !empty($data['form_scope'])
            && User::whereNull('form_scope')->count() <= 1
        ) {
            return back()
                ->withInput()
                ->withErrors(['form_scope' => 'Não é possível remover o último Super Admin do sistema.']);
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Só o Super Admin pode alterar a área de um usuário.
        if ($actingUser->isSuperAdmin() && array_key_exists('form_scope', $data)) {
            $updateData['form_scope'] = $data['form_scope'] ?: null;
        }

        if ($actingUser->isSuperAdmin() && array_key_exists('admin_role', $data)) {
            $updateData['admin_role'] = $this->normalizeAdminRole($data['admin_role'] ?? null);
        }

        $user->update($updateData);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário atualizado com sucesso.');
    }

    private function normalizeAdminRole(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            $values = array_values(array_unique(array_filter(array_map('strval', $value), fn ($item) => $item !== '')));
            return $values === [] ? null : implode(',', $values);
        }

        if (is_string($value)) {
            $values = array_filter(array_map('trim', explode(',', $value)));
            return $values === [] ? null : implode(',', $values);
        }

        return null;
    }

    private function authorizeManage(User $actingUser, User $targetUser): void
    {
        if ($actingUser->isSuperAdmin()) {
            return;
        }

        abort_unless(
            $targetUser->form_scope === $actingUser->form_scope,
            403,
            'Você não tem permissão para gerenciar este usuário.'
        );
    }
}