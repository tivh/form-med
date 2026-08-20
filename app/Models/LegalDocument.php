<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    protected $fillable = [
        'document_key',
        'person_type',
        'title',
        'version',
        'text',
    ];

    public static function getDocument(string $documentKey, string $personType, string $title, string $fallbackText = '', string $fallbackVersion = 'v1.0'): array
    {
        $document = static::query()
            ->where('document_key', $documentKey)
            ->where('person_type', $personType)
            ->first();

        if ($document) {
            return [
                'key' => $document->document_key,
                'title' => $document->title ?: $title,
                'text' => $document->text ?? $fallbackText,
                'version' => $document->version ?: $fallbackVersion,
                'updated_at' => $document->updated_at,
            ];
        }

        return [
            'key' => $documentKey,
            'title' => $title,
            'text' => $fallbackText,
            'version' => $fallbackVersion,
            'updated_at' => null,
        ];
    }

    public static function saveDocument(string $documentKey, string $personType, string $title, ?string $text, ?string $version): self
    {
        return static::updateOrCreate(
            [
                'document_key' => $documentKey,
                'person_type' => $personType,
            ],
            [
                'title' => $title,
                'text' => $text ?? '',
                'version' => $version ?: 'v1.0',
            ]
        );
    }
}
