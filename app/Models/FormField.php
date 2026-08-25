<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_step_id',
        'name',
        'label',
        'type',
        'placeholder',
        'help_text',
        'is_required',
        'options',
        'validation_rules',
        'conditional_logic',
        'grid_columns',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'options' => 'array',
            'validation_rules' => 'array',
            'conditional_logic' => 'array',
            'grid_columns' => 'integer',
            'order_index' => 'integer',
        ];
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(FormStep::class, 'form_step_id');
    }
}
