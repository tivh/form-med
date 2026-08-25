<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomFormResource\Pages;
use App\Models\Area;
use App\Models\CustomForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CustomFormResource extends Resource
{
    protected static ?string $model = CustomForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Formulários & Cadastros';

    protected static ?string $navigationLabel = 'Formulários & Campos';

    protected static ?string $modelLabel = 'Formulário';

    protected static ?string $pluralModelLabel = 'Formulários';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('FormTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Configurações Gerais')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('area_id')
                                            ->label('Área / Módulo')
                                            ->options(fn () => auth()->user()?->isSuperAdmin()
                                                ? Area::pluck('name', 'id')
                                                : auth()->user()?->areas()->pluck('name', 'areas.id') ?? [])
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Forms\Components\Select::make('status')
                                            ->label('Status do Formulário')
                                            ->options([
                                                'online' => 'Online (Ativo para preenchimento)',
                                                'offline' => 'Offline (Desativado temporariamente)',
                                            ])
                                            ->default('online')
                                            ->required(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título do Formulário')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                if ($operation === 'create') {
                                                    $set('slug', Str::slug($state));
                                                }
                                            }),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug / URL Pública')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->helperText('Identificador na URL: /forms/{slug}'),
                                    ]),

                                Forms\Components\Textarea::make('description')
                                    ->label('Descrição / Subtítulo')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Toggle::make('is_multi_step')
                                            ->label('Formulário em Etapas (Multi-Step)')
                                            ->default(true)
                                            ->helperText('Divide as seções em abas/etapas sequenciais.'),

                                        Forms\Components\TextInput::make('submission_context')
                                            ->label('Contexto da Submissão')
                                            ->default('public')
                                            ->helperText('Ex: public, rh, financeiro'),

                                        Forms\Components\Select::make('restrict_registration_type')
                                            ->label('Restrição de Tipo de Cadastro')
                                            ->options([
                                                '' => 'Sem restrição (Permite PF e PJ)',
                                                'pj' => 'Apenas Pessoa Jurídica (PJ)',
                                                'pf' => 'Apenas Pessoa Física (PF)',
                                            ])
                                            ->nullable(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Construtor de Etapas e Campos')
                            ->icon('heroicon-o-squares-plus')
                            ->schema([
                                Forms\Components\Repeater::make('steps')
                                    ->relationship()
                                    ->label('Etapas do Formulário')
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nova Etapa')
                                    ->collapsible()
                                    ->cloneable()
                                    ->orderColumn('order_index')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Nome da Etapa / Seção')
                                                    ->required()
                                                    ->placeholder('Ex: Dados Cadastrais, Compliance, Termos...'),

                                                Forms\Components\TextInput::make('description')
                                                    ->label('Descrição da Etapa')
                                                    ->placeholder('Texto explicativo para o usuário'),
                                            ]),

                                        Forms\Components\Repeater::make('fields')
                                            ->relationship()
                                            ->label('Campos e Perguntas desta Etapa')
                                            ->itemLabel(fn (array $state): ?string => ($state['label'] ?? 'Novo Campo') . ' [' . ($state['type'] ?? 'text') . ']')
                                            ->collapsible()
                                            ->cloneable()
                                            ->orderColumn('order_index')
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Chave / Nome Técnico')
                                                            ->required()
                                                            ->placeholder('ex: razao_social, telefone')
                                                            ->helperText('Nome do campo para armazenamento.'),

                                                        Forms\Components\TextInput::make('label')
                                                            ->label('Rótulo / Pergunta')
                                                            ->required()
                                                            ->placeholder('Ex: Digite a Razão Social da Empresa'),

                                                        Forms\Components\Select::make('type')
                                                            ->label('Tipo de Campo')
                                                            ->options([
                                                                'text' => 'Texto Simples',
                                                                'email' => 'E-mail',
                                                                'tel' => 'Telefone / WhatsApp',
                                                                'date' => 'Data',
                                                                'textarea' => 'Texto Longo (Textarea)',
                                                                'radio' => 'Múltipla Escolha (Opção Única / Radio)',
                                                                'checkbox' => 'Caixas de Seleção (Múltiplas Opções)',
                                                                'select' => 'Lista Suspensa (Select)',
                                                                'file' => 'Upload de Arquivo (PDF / Imagem)',
                                                            ])
                                                            ->default('text')
                                                            ->required()
                                                            ->live(),
                                                    ]),

                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('placeholder')
                                                            ->label('Texto de Ajuda / Placeholder')
                                                            ->placeholder('Ex: 00.000.000/0000-00'),

                                                        Forms\Components\Select::make('grid_columns')
                                                            ->label('Largura do Campo na Tela')
                                                            ->options([
                                                                1 => 'Largura Completa (1 Coluna)',
                                                                2 => 'Meia Largura (2 Colunas)',
                                                            ])
                                                            ->default(2),

                                                        Forms\Components\Toggle::make('is_required')
                                                            ->label('Campo Obrigatório')
                                                            ->default(false),
                                                    ]),

                                                Forms\Components\KeyValue::make('options')
                                                    ->label('Opções de Resposta (para Radio / Select / Checkbox)')
                                                    ->keyLabel('Valor / Chave')
                                                    ->valueLabel('Rótulo visível')
                                                    ->formatStateUsing(function ($state) {
                                                        if (!is_array($state)) {
                                                            return [];
                                                        }
                                                        $normalized = [];
                                                        foreach ($state as $key => $val) {
                                                            if (is_array($val)) {
                                                                $itemKey = $val['value'] ?? $key;
                                                                $itemLabel = $val['label'] ?? ($val['value'] ?? '');
                                                                $normalized[(string) $itemKey] = (string) $itemLabel;
                                                            } else {
                                                                $normalized[(string) $key] = (string) $val;
                                                            }
                                                        }
                                                        return $normalized;
                                                    })
                                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['radio', 'select', 'checkbox'])),
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título do Formulário')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Slug copiado!'),

                Tables\Columns\TextColumn::make('area.name')
                    ->label('Área')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('steps_count')
                    ->label('Etapas')
                    ->counts('steps')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'success',
                        'offline' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Filtrar por Área')
                    ->relationship('area', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Visualizar')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (CustomForm $record): string => route('forms.show', $record->slug))
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

    public static function getEloquentQuery(): Builder
    {
        if (CustomForm::count() === 0) {
            (new \Database\Seeders\TransferExistingFormsSeeder())->run();
        }

        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && !$user->isSuperAdmin()) {
            $userAreaIds = $user->areas->pluck('id')->toArray();
            $query->whereIn('area_id', $userAreaIds);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomForms::route('/'),
            'create' => Pages\CreateCustomForm::route('/create'),
            'edit' => Pages\EditCustomForm::route('/{record}/edit'),
        ];
    }
}
