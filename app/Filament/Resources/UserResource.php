<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = '用户管理';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('easemob_username')
                    ->maxLength(255),
                Forms\Components\TextInput::make('easemob_password')
                    ->password()
                    ->maxLength(255),
                Forms\Components\TextInput::make('easemob_uuid')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_online')
                    ->required(),
                Forms\Components\DateTimePicker::make('last_seen_at'),
                Forms\Components\TextInput::make('avatar')
                    ->maxLength(255),
                Forms\Components\TextInput::make('gender')
                    ->maxLength(255),
                Forms\Components\TextInput::make('age')
                    ->numeric(),
                Forms\Components\TextInput::make('height')
                    ->maxLength(255),
                Forms\Components\TextInput::make('weight')
                    ->maxLength(255),
                Forms\Components\TextInput::make('hobbies')
                    ->maxLength(255),
                Forms\Components\TextInput::make('specialty')
                    ->maxLength(255),
                Forms\Components\Textarea::make('love_declaration')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('活跃度'),
                Forms\Components\TextInput::make('coins')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('金币')
                    ->prefix('💰')
                    ->hint('可直接修改金币数量'),
                Forms\Components\TextInput::make('total_coins_spent')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('累计消费金币')
                    ->disabled(),
                Forms\Components\TextInput::make('total_coins_recharged')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('累计充值金币')
                    ->disabled(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\Toggle::make('is_admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('easemob_username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('easemob_uuid')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_online')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_seen_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('avatar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->searchable(),
                Tables\Columns\TextColumn::make('weight')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hobbies')
                    ->searchable(),
                Tables\Columns\TextColumn::make('specialty')
                    ->searchable(),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable()
                    ->label('活跃度'),
                Tables\Columns\TextColumn::make('coins')
                    ->numeric()
                    ->sortable()
                    ->label('金币')
                    ->prefix('💰'),
                Tables\Columns\TextColumn::make('total_coins_spent')
                    ->numeric()
                    ->sortable()
                    ->label('累计消费')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_coins_recharged')
                    ->numeric()
                    ->sortable()
                    ->label('累计充值')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
