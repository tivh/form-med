<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxRegimeSubmissionResource\Pages;
use App\Models\TaxRegimeSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxRegimeSubmissionResource extends Resource
{
    protected static ?string $model = TaxRegimeSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Formulários & Cadastros';

    protected static ?string $navigationLabel = 'Regime Tributário (Fiscal)';

    protected static ?string $modelLabel = 'Regime Tributário';

    protected static ?string $pluralModelLabel = 'Submissões de Regime Tributário';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('regime-tributario') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('razao_social')
                                    ->label('Razão Social')
                                    ->disabled(),

                                Forms\Components\TextInput::make('cnpj')
                                    ->label('CNPJ')
                                    ->disabled(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('regime_tributario')
                                    ->label('Regime Tributário Informado')
                                    ->disabled(),

                                Forms\Components\TextInput::make('enquadramento_anexo')
                                    ->label('Anexo de Enquadramento')
                                    ->disabled(),
                            ]),

                        Forms\Components\Toggle::make('verified')
                            ->label('Verificado / Validado pelo Setor Fiscal & Financeiro')
                            ->helperText('Marque para atestar a conformidade fiscal do fornecedor.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Exportar para Excel (XLSX)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (): string => route('admin.tax-regime.export'))
                    ->openUrlInNewTab(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('razao_social')
                    ->label('Razão Social')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('regime_tributario')
                    ->label('Regime Tributário')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'simples_nacional' => 'Simples Nacional',
                        'lucro_presumido' => 'Lucro Presumido',
                        'lucro_real' => 'Lucro Real',
                        'mei' => 'MEI',
                        default => $state ?? '-',
                    }),

                Tables\Columns\IconColumn::make('verified')
                    ->label('Verificado')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('regime_tributario')
                    ->label('Regime Tributário')
                    ->options([
                        'simples_nacional' => 'Simples Nacional',
                        'lucro_presumido' => 'Lucro Presumido',
                        'lucro_real' => 'Lucro Real',
                        'mei' => 'MEI',
                    ]),

                Tables\Filters\TernaryFilter::make('verified')
                    ->label('Status de Verificação')
                    ->placeholder('Todos')
                    ->trueLabel('Verificados')
                    ->falseLabel('Pendentes'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_verified')
                    ->label(fn (TaxRegimeSubmission $record): string => $record->verified ? 'Desmarcar' : 'Validar')
                    ->icon(fn (TaxRegimeSubmission $record): string => $record->verified ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (TaxRegimeSubmission $record): string => $record->verified ? 'gray' : 'success')
                    ->action(function (TaxRegimeSubmission $record): void {
                        $record->update(['verified' => !$record->verified]);
                    }),

                Tables\Actions\EditAction::make()->label('Detalhes'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxRegimeSubmissions::route('/'),
            'edit' => Pages\EditTaxRegimeSubmission::route('/{record}/edit'),
        ];
    }
}
