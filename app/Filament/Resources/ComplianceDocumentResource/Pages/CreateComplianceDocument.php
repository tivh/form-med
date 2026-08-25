<?php

namespace App\Filament\Resources\ComplianceDocumentResource\Pages;

use App\Filament\Resources\ComplianceDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComplianceDocument extends CreateRecord
{
    protected static string $resource = ComplianceDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['file_path'])) {
            $filePath = storage_path('app/public/' . $data['file_path']);
            if (file_exists($filePath)) {
                $data['file_size'] = filesize($filePath);
                $data['mime_type'] = mime_content_type($filePath);
            }
        }

        return $data;
    }
}
