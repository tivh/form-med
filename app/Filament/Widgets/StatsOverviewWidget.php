<?php

namespace App\Filament\Widgets;

use App\Models\CustomForm;
use App\Models\FormSubmission;
use App\Models\TaxRegimeSubmission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Schema;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $user = auth()->user();
        $stats = [];

        // 1. Submissões Compliance (se o usuário tiver acesso)
        if ($user && ($user->isSuperAdmin() || $user->canAccess('form-med'))) {
            $complianceQuery = FormSubmission::query();
            if (!$user->isSuperAdmin() && $user->allowedClassifications() !== []) {
                $complianceQuery->whereIn('classification', $user->allowedClassifications());
            }

            $totalCompliance = $complianceQuery->count();
            $verifiedCompliance = (clone $complianceQuery)->where('verified', true)->count();

            $stats[] = Stat::make('Submissões Compliance', (string) $totalCompliance)
                ->description("{$verifiedCompliance} verificadas")
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger')
                ->chart([3, 7, 6, 9, 12, 10, $totalCompliance]);
        }

        // 2. Regime Tributário (se o usuário tiver acesso)
        if ($user && ($user->isSuperAdmin() || $user->canAccess('regime-tributario'))) {
            if (Schema::hasTable('tax_regime_submissions')) {
                $totalTax = TaxRegimeSubmission::count();
                $verifiedTax = TaxRegimeSubmission::where('verified', true)->count();

                $stats[] = Stat::make('Regime Tributário', (string) $totalTax)
                    ->description("{$verifiedTax} validadas")
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('primary')
                    ->chart([2, 4, 5, 8, 7, 10, $totalTax]);
            }
        }

        // 3. Formulários Ativos
        if ($user && $user->isSuperAdmin()) {
            if (Schema::hasTable('custom_forms')) {
                $onlineForms = CustomForm::online()->count();
                $totalForms = CustomForm::count();

                $stats[] = Stat::make('Formulários Online', (string) $onlineForms)
                    ->description("{$totalForms} cadastrados")
                    ->descriptionIcon('heroicon-m-document-text')
                    ->color('success');
            }
        }

        return $stats;
    }
}
