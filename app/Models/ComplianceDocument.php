<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceDocument extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'file_path',
        'file_original_name',
        'file_size',
        'mime_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function formattedFileSize(): string
    {
        $bytes = $this->file_size;

        if ($bytes < 1024) {
            return "{$bytes} B";
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 1).' MB';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
