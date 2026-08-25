<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormSubmissionResource\Pages;
use App\Models\FormSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Formulários & Cadastros';

    protected static ?string $navigationLabel = 'Submissões Compliance';

    protected static ?string $modelLabel = 'Submissão de Compliance';

    protected static ?string $pluralModelLabel = 'Submissões de Compliance';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('form-med') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('SubmissionTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Dados Cadastrais')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('nome')
                                            ->label('Nome / Razão Social')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('razao_social')
                                            ->label('Razão Social')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('nome_fantasia')
                                            ->label('Nome Fantasia')
                                            ->disabled(),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('cnpj')
                                            ->label('CNPJ')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('cpf')
                                            ->label('CPF')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('classification')
                                            ->label('Classificação')
                                            ->disabled(),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->label('E-mail')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('telefone')
                                            ->label('Telefone')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('profissao')
                                            ->label('Profissão / Ramo')
                                            ->disabled(),
                                    ]),

                                Forms\Components\Textarea::make('endereco')
                                    ->label('Endereço Completo')
                                    ->disabled()
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('dados_bancarios')
                                    ->label('Dados Bancários')
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Compliance & Integridade')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('law_12846_compliant')
                                            ->label('Conformidade Lei Anticorrupção (12.846/13)')
                                            ->disabled(),

                                        Forms\Components\Toggle::make('lgpd_compliant')
                                            ->label('Conformidade LGPD (13.709/18)')
                                            ->disabled(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('public_power_relatives')
                                            ->label('Parentesco com Agente Público')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('internal_relationships')
                                            ->label('Relação com Colaboradores Internos')
                                            ->disabled(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('employee_shareholding')
                                            ->label('Participação Societária')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('conflict_situation')
                                            ->label('Situação de Conflito de Interesses')
                                            ->disabled(),
                                    ]),

                                Forms\Components\Textarea::make('investigated_for')
                                    ->label('Histórico de Investigações / Processos')
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Status & Validação')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Forms\Components\Toggle::make('verified')
                                    ->label('Submissão Verificada / Aprovada pelo Compliance')
                                    ->helperText('Marque esta opção para aprovar formalmente o cadastro do fornecedor.')
                                    ->default(false),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('testemunha_nome')
                                            ->label('Testemunha / Responsável')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('testemunha_email')
                                            ->label('E-mail da Testemunha')
                                            ->disabled(),

                                        Forms\Components\DateTimePicker::make('compliance_aceito_em')
                                            ->label('Aceite Registrado em')
                                            ->disabled(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('submitted_ip')
                                            ->label('IP de Origem')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('submitted_location')
                                            ->label('Localização / Origem')
                                            ->disabled(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
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
                    ->url(fn (): string => route('admin.submissions.export'))
                    ->openUrlInNewTab(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('displayName')
                    ->label('Nome / Razão Social')
                    ->state(fn (FormSubmission $record) => $record->razao_social ?: $record->nome)
                    ->searchable(['nome', 'razao_social', 'nome_fantasia'])
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('displayDocument')
                    ->label('Documento')
                    ->state(fn (FormSubmission $record) => $record->cnpj ?: $record->cpf)
                    ->searchable(['cnpj', 'cpf']),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('classification')
                    ->label('Classificação')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pj-rh' => 'warning',
                        'pj' => 'info',
                        'pf' => 'success',
                        default => 'gray',
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
                Tables\Filters\SelectFilter::make('classification')
                    ->label('Classificação')
                    ->options([
                        'pj' => 'PJ Principal',
                        'pj-rh' => 'PJ RH',
                        'pf' => 'PF',
                    ]),

                Tables\Filters\TernaryFilter::make('verified')
                    ->label('Status de Verificação')
                    ->placeholder('Todos')
                    ->trueLabel('Verificados')
                    ->falseLabel('Pendentes'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_verified')
                    ->label(fn (FormSubmission $record): string => $record->verified ? 'Desmarcar' : 'Verificar')
                    ->icon(fn (FormSubmission $record): string => $record->verified ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (FormSubmission $record): string => $record->verified ? 'gray' : 'success')
                    ->action(function (FormSubmission $record): void {
                        $record->update(['verified' => !$record->verified]);
                    }),

                Tables\Actions\Action::make('download_zip')
                    ->label('Documentos')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (FormSubmission $record): string => route('admin.submissions.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('print')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (FormSubmission $record): string => route('admin.submissions.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()->label('Detalhes'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->latest();
        $user = auth()->user();

        if ($user && !$user->isSuperAdmin()) {
            $allowed = $user->allowedClassifications();
            if ($allowed !== []) {
                $query->whereIn('classification', $allowed);
            }
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormSubmissions::route('/'),
            'edit' => Pages\EditFormSubmission::route('/{record}/edit'),
        ];
    }
}
