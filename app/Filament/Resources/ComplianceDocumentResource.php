<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplianceDocumentResource\Pages;
use App\Models\ComplianceDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComplianceDocumentResource extends Resource
{
    protected static ?string $model = ComplianceDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Documentos & Termos';

    protected static ?string $navigationLabel = 'Documentos Públicos';

    protected static ?string $modelLabel = 'Documento Institucional';

    protected static ?string $pluralModelLabel = 'Documentos Institucionais';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('form-med') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título do Documento')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Código de Ética e Conduta 2026'),

                                Forms\Components\TextInput::make('category')
                                    ->label('Categoria')
                                    ->placeholder('Ex: Compliance, LGPD, Integridade')
                                    ->maxLength(100),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrição do Documento')
                            ->rows(3)
                            ->placeholder('Resumo explicativo sobre o documento disponibilizado para download'),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('Arquivo PDF / Documento')
                            ->disk('public')
                            ->directory('compliance-docs')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(20480)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->storeFileNamesIn('file_original_name')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Ordem de Exibição')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Documento Ativo (Visível na página pública)')
                                    ->default(true),
                            ]),
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

                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Baixar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (ComplianceDocument $record): string => route('compliance.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplianceDocuments::route('/'),
            'create' => Pages\CreateComplianceDocument::route('/create'),
            'edit' => Pages\EditComplianceDocument::route('/{record}/edit'),
        ];
    }
}
