<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CustomForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'slug',
        'title',
        'description',
        'status',
        'is_multi_step',
        'submission_context',
        'restrict_registration_type',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_multi_step' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(FormStep::class)->orderBy('order_index');
    }

    public function fields(): HasManyThrough
    {
        return $this->hasManyThrough(FormField::class, FormStep::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }
}
