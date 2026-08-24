<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'form_scope',
        'admin_role',
    ];

    /**
     * Relacionamento com as áreas permitidas para este usuário.
     */
    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'area_user')
            ->withPivot('permissions')
            ->withTimestamps();
    }

    /**
     * Retorna todas as áreas ativas acessíveis por este usuário.
     */
    public function accessibleAreas()
    {
        if ($this->isSuperAdmin()) {
            return Area::active()->get();
        }

        return $this->areas()->where('is_active', true)->get();
    }

    /**
     * Verifica se o usuário pode acessar uma determinada área/formulário pelo slug.
     */
    public function canAccess(string $formSlug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Verifica na relação de áreas vinculadas
        if ($this->relationLoaded('areas')) {
            if ($this->areas->contains('slug', $formSlug)) {
                return true;
            }
        } else {
            if ($this->areas()->where('slug', $formSlug)->exists()) {
                return true;
            }
        }

        // Fallback legado para form_scope
        return $this->form_scope === $formSlug;
    }

    /**
     * Verifica se o usuário é Super Admin (acesso total a todas as áreas e configurações).
     */
    public function isSuperAdmin(): bool
    {
        if ($this->is_super_admin !== null) {
            return (bool) $this->is_super_admin;
        }

        // Fallback para registros legados antes da migração
        return $this->form_scope === null;
    }

    /**
     * Retorna as opções de classificação configuradas para o usuário (ex: no Compliance).
     */
    public function adminRoleOptions(): array
    {
        // 1. Tentar obter das permissões do vínculo na tabela pivot
        $complianceArea = $this->relationLoaded('areas')
            ? $this->areas->firstWhere('slug', 'form-med')
            : $this->areas()->where('slug', 'form-med')->first();

        if ($complianceArea && !empty($complianceArea->pivot->permissions)) {
            $perms = is_array($complianceArea->pivot->permissions)
                ? $complianceArea->pivot->permissions
                : json_decode($complianceArea->pivot->permissions, true);

            if (isset($perms['allowed_classifications']) && is_array($perms['allowed_classifications'])) {
                return array_values(array_filter(array_map('strval', $perms['allowed_classifications']), fn ($v) => $v !== ''));
            }
        }

        // 2. Fallback para campo legado admin_role
        if (empty($this->admin_role)) {
            return [];
        }

        $legacyMap = [
            'rh' => ['pj-rh'],
            'juridico' => ['pj', 'pf'],
        ];

        if (is_array($this->admin_role)) {
            return array_values(array_filter(array_map('strval', $this->admin_role), fn ($value) => $value !== ''));
        }

        $roleValue = (string) $this->admin_role;
        if (isset($legacyMap[$roleValue])) {
            return $legacyMap[$roleValue];
        }

        $values = array_filter(array_map('trim', explode(',', $roleValue)));
        if ($values !== []) {
            return array_values($values);
        }

        return [];
    }

    /**
     * Retorna as classificações normalizadas permitidas.
     */
    public function allowedClassifications(): array
    {
        $options = $this->adminRoleOptions();

        if ($options === []) {
            return [];
        }

        $normalized = array_map(function ($value) {
            return match ($value) {
                'pj_diverso', 'pj' => 'pj',
                'pj_colaborador', 'pj-rh' => 'pj-rh',
                default => $value,
            };
        }, $options);

        return array_values(array_unique(array_filter($normalized, fn ($value) => in_array($value, ['pj', 'pj-rh', 'pf'], true))));
    }

    public function isRh(): bool
    {
        return in_array('pj-rh', $this->allowedClassifications(), true);
    }

    public function isJuridico(): bool
    {
        return in_array('pj', $this->allowedClassifications(), true) || in_array('pf', $this->allowedClassifications(), true);
    }

    /**
     * Valida se o usuário tem permissão para visualizar uma submissão de formulário.
     */
    public function canViewSubmission(
        \App\Models\FormSubmission $submission
    ): bool {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->canAccess('form-med')) {
            return false;
        }

        $allowed = $this->allowedClassifications();

        if ($allowed === []) {
            return true;
        }

        return in_array($submission->classification, $allowed, true);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }
}
