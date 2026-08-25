<?php

namespace App\Filament\Resources\ComplianceDocumentResource\Pages;

use App\Filament\Resources\ComplianceDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplianceDocument extends EditRecord
{
    protected static string $resource = ComplianceDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
