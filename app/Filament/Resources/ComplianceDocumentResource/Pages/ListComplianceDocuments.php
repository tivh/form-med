<?php

namespace App\Filament\Resources\ComplianceDocumentResource\Pages;

use App\Filament\Resources\ComplianceDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplianceDocuments extends ListRecords
{
    protected static string $resource = ComplianceDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo Documento'),
        ];
    }
}
