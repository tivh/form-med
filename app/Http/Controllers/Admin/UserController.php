<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
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
        abort_unless($actingUser->isSuperAdmin(), 403, 'Acesso restrito ao Super Admin.');

        $query = User::query()->with('areas')->latest();
        $users = $query->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
            'isSuperAdmin' => true,
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Acesso restrito ao Super Admin.');

        $areas = Area::active()->get();

        return view('admin.users.create', [
            'areas' => $areas,
            'isSuperAdmin' => true,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $actingUser = $request->user();
        abort_unless($actingUser->isSuperAdmin(), 403, 'Acesso restrito ao Super Admin.');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($actingUser->isSuperAdmin()) {
            $rules['is_super_admin'] = ['nullable', 'boolean'];
            $rules['areas'] = ['nullable', 'array'];
            $rules['areas.*'] = ['string'];
            $rules['form_scope'] = ['nullable', 'string'];
            $rules['admin_role'] = ['nullable', 'array'];
            $rules['admin_role.*'] = ['string', 'in:pj,pj-rh,pf,pj_diverso,pj_colaborador'];
        }

        $data = $request->validate($rules);

        $isSuperAdmin = $actingUser->isSuperAdmin() && !empty($data['is_super_admin']);

        // Se for Super Admin, não precisa de form_scope
        $formScope = null;
        if (!$isSuperAdmin) {
            if ($actingUser->isSuperAdmin()) {
                $selectedAreas = $data['areas'] ?? (!empty($data['form_scope']) ? [$data['form_scope']] : []);
                $formScope = count($selectedAreas) === 1 ? $selectedAreas[0] : (count($selectedAreas) > 1 ? implode(',', $selectedAreas) : null);
            } else {
                $formScope = $actingUser->form_scope;
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_super_admin' => $isSuperAdmin,
            'form_scope' => $formScope,
            'admin_role' => $this->normalizeAdminRole($data['admin_role'] ?? null),
        ]);

        if ($actingUser->isSuperAdmin()) {
            $this->syncUserAreas($user, $isSuperAdmin, $data['areas'] ?? [], $data['admin_role'] ?? null);
        } elseif ($actingUser->areas()->exists()) {
            // Se usuário escopado criar outro usuário, herda a mesma área
            $user->areas()->sync($actingUser->areas()->pluck('areas.id'));
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário criado com sucesso.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeManage($request->user(), $user);

        $areas = Area::active()->get();
        $user->load('areas');

        return view('admin.users.edit', [
            'user' => $user,
            'areas' => $areas,
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
            $rules['is_super_admin'] = ['nullable', 'boolean'];
            $rules['areas'] = ['nullable', 'array'];
            $rules['areas.*'] = ['string'];
            $rules['form_scope'] = ['nullable', 'string'];
            $rules['admin_role'] = ['nullable', 'array'];
            $rules['admin_role.*'] = ['string', 'in:pj,pj-rh,pf,pj_diverso,pj_colaborador'];
        }

        $data = $request->validate($rules);

        $isSuperAdmin = $actingUser->isSuperAdmin()
            ? (bool) ($data['is_super_admin'] ?? false)
            : $user->isSuperAdmin();

        // Impede o último Super Admin de se rebaixar e travar o próprio acesso.
        if (
            $actingUser->isSuperAdmin()
            && $user->id === $actingUser->id
            && !$isSuperAdmin
            && User::where('is_super_admin', true)->count() <= 1
        ) {
            return back()
                ->withInput()
                ->withErrors(['is_super_admin' => 'Não é possível remover o último Super Admin do sistema.']);
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if ($actingUser->isSuperAdmin()) {
            $updateData['is_super_admin'] = $isSuperAdmin;

            $selectedAreas = $data['areas'] ?? (!empty($data['form_scope']) ? [$data['form_scope']] : []);
            $updateData['form_scope'] = count($selectedAreas) === 1 ? $selectedAreas[0] : null;
            $updateData['admin_role'] = $this->normalizeAdminRole($data['admin_role'] ?? null);

            $this->syncUserAreas($user, $isSuperAdmin, $selectedAreas, $data['admin_role'] ?? null);
        }

        $user->update($updateData);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário atualizado com sucesso.');
    }

    private function syncUserAreas(User $user, bool $isSuperAdmin, array $selectedAreaSlugs, mixed $rawAdminRole): void
    {
        if ($isSuperAdmin) {
            // Super Admin tem acesso a todas as áreas sem precisar de registros pivot restritivos
            $user->areas()->detach();
            return;
        }

        $allAreas = Area::whereIn('slug', $selectedAreaSlugs)->get();
        $syncData = [];

        $normalizedClassifications = $this->extractClassifications($rawAdminRole);

        foreach ($allAreas as $area) {
            $permissions = null;
            if ($area->slug === 'form-med' && $normalizedClassifications !== []) {
                $permissions = json_encode([
                    'allowed_classifications' => $normalizedClassifications,
                ]);
            }

            $syncData[$area->id] = [
                'permissions' => $permissions,
            ];
        }

        $user->areas()->sync($syncData);
    }

    private function extractClassifications(mixed $value): array
    {
        if (empty($value)) {
            return [];
        }

        $array = is_array($value) ? $value : explode(',', (string) $value);
        $normalized = array_map(function ($item) {
            $trimmed = trim((string) $item);
            return match ($trimmed) {
                'pj_diverso', 'pj' => 'pj',
                'pj_colaborador', 'pj-rh' => 'pj-rh',
                default => $trimmed,
            };
        }, $array);

        return array_values(array_unique(array_filter($normalized, fn ($v) => in_array($v, ['pj', 'pj-rh', 'pf'], true))));
    }

    private function normalizeAdminRole(mixed $value): ?string
    {
        $classifications = $this->extractClassifications($value);
        return $classifications === [] ? null : implode(',', $classifications);
    }

    private function authorizeManage(User $actingUser, User $targetUser): void
    {
        abort_unless($actingUser->isSuperAdmin(), 403, 'Acesso restrito ao Super Admin.');
    }
}