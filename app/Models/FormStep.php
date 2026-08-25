<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_form_id',
        'title',
        'description',
        'order_index',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order_index');
    }
}
