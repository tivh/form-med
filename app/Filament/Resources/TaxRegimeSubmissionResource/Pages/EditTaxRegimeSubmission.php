<?php

namespace App\Filament\Resources\TaxRegimeSubmissionResource\Pages;

use App\Filament\Resources\TaxRegimeSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxRegimeSubmission extends EditRecord
{
    protected static string $resource = TaxRegimeSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
