<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalDocumentResource\Pages;
use App\Models\LegalDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LegalDocumentResource extends Resource
{
    protected static ?string $model = LegalDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Documentos & Termos';

    protected static ?string $navigationLabel = 'Termos & Políticas Legais';

    protected static ?string $modelLabel = 'Termo Legal';

    protected static ?string $pluralModelLabel = 'Termos & Políticas Legais';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título do Documento')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('document_key')
                                    ->label('Identificador Chave')
                                    ->options([
                                        'code_of_conduct' => 'Código de Conduta',
                                        'integrity_policy' => 'Política de Integridade',
                                        'data_protection' => 'Proteção de Dados (LGPD)',
                                        'terms_pf' => 'Termos Gerais PF',
                                        'terms_pj' => 'Termos Gerais PJ',
                                    ])
                                    ->required(),

                                Forms\Components\Select::make('person_type')
                                    ->label('Tipo de Pessoa')
                                    ->options([
                                        'pj' => 'Pessoa Jurídica (PJ)',
                                        'pf' => 'Pessoa Física (PF)',
                                        'all' => 'Geral (Todos)',
                                    ])
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('version')
                            ->label('Versão do Documento')
                            ->placeholder('Ex: v1.0, v2.1')
                            ->default('v1.0')
                            ->required(),

                        Forms\Components\Textarea::make('text')
                            ->label('Texto Completo dos Termos / Política')
                            ->rows(14)
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Este texto será exibido dentro dos modais de leitura e aceite dos formulários públicos.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('document_key')
                    ->label('Chave')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('person_type')
                    ->label('Aplicação')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pj' => 'info',
                        'pf' => 'success',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                Tables\Columns\TextColumn::make('version')
                    ->label('Versão')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Edição')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('person_type')
                    ->label('Filtrar por Tipo')
                    ->options([
                        'pj' => 'PJ',
                        'pf' => 'PF',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLegalDocuments::route('/'),
            'create' => Pages\CreateLegalDocument::route('/create'),
            'edit' => Pages\EditLegalDocument::route('/{record}/edit'),
        ];
    }
}
