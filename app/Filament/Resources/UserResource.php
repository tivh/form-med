<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Area;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Usuários & Permissões';

    protected static ?string $modelLabel = 'Usuário';

    protected static ?string $pluralModelLabel = 'Usuários';

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
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome Completo')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('E-mail')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('Senha')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->maxLength(255)
                                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Deixe em branco para manter a senha atual.' : null),

                                Forms\Components\Toggle::make('is_super_admin')
                                    ->label('Super Administrador (Acesso Total)')
                                    ->helperText('Permite acesso irrestrito a todas as áreas, formulários e configurações.')
                                    ->live(),
                            ]),

                        Forms\Components\Section::make('Permissões de Acesso por Área')
                            ->description('Selecione quais áreas este usuário poderá visualizar e gerenciar.')
                            ->visible(fn (Forms\Get $get) => !$get('is_super_admin'))
                            ->schema([
                                Forms\Components\CheckboxList::make('areas')
                                    ->relationship('areas', 'name')
                                    ->label('Áreas / Módulos')
                                    ->columns(2)
                                    ->required(fn (Forms\Get $get) => !$get('is_super_admin')),

                                Forms\Components\CheckboxList::make('allowed_classifications')
                                    ->label('Restrição de Classificação de Fornecedores (opcional)')
                                    ->options([
                                        'pj' => 'PJ Principal (Fornecedores Diversos)',
                                        'pj-rh' => 'PJ RH (Colaboradores)',
                                        'pf' => 'Pessoa Física (PF)',
                                    ])
                                    ->helperText('Deixe vazio para permitir todas as classificações das áreas atribuídas.')
                                    ->columns(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('areas.name')
                    ->label('Áreas de Acesso')
                    ->badge()
                    ->color(fn ($record) => $record->isSuperAdmin() ? 'danger' : 'primary')
                    ->formatStateUsing(fn ($state, $record) => $record->isSuperAdmin() ? 'Super Admin (Acesso Total)' : $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
