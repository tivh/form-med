<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'form_scope',
        'admin_role',
    ];

    public function canAccess(string $formSlug): bool
    {
        return $this->form_scope === null || $this->form_scope === $formSlug;
    }

    public function isSuperAdmin(): bool
    {
        return $this->form_scope === null;
    }

    public function adminRoleOptions(): array
    {
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

    public function canViewSubmission(
        \App\Models\FormSubmission $submission
    ): bool {
        if ($this->isSuperAdmin()) {
            return true;
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
        ];
    }
}
