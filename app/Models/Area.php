<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'default_route',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'area_user')
            ->withPivot('permissions')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getNavItemsAttribute(): array
    {
        $configArea = config("admin_areas.{$this->slug}");
        if ($configArea && isset($configArea['nav_items'])) {
            return $configArea['nav_items'];
        }

        if (!empty($this->default_route)) {
            return [
                [
                    'route' => $this->default_route,
                    'pattern' => $this->default_route,
                    'label' => $this->name,
                ],
            ];
        }

        return [];
    }

    public function getCountAttribute(): int
    {
        $configArea = config("admin_areas.{$this->slug}");
        if ($configArea && isset($configArea['count_model']) && class_exists($configArea['count_model'])) {
            return $configArea['count_model']::count();
        }

        return 0;
    }
}
