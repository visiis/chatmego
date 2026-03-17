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
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
                Forms\Components\Placeholder::make('avatar_display')
                    ->label('头像')
                    ->content(function ($record) {
                        if ($record && $record->avatar) {
                            return new \Illuminate\Support\HtmlString(
                                '<img src="' . asset('storage/' . $record->avatar) . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">'
                            );
                        }
                        return new \Illuminate\Support\HtmlString(
                            '<img src="' . asset('images/default-avatar.svg') . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">'
                        );
                    })
                    ->visible(fn ($record) => $record && $record->exists),
                Forms\Components\Toggle::make('delete_avatar')
                    ->label('删除头像')
                    ->default(false)
                    ->visible(fn ($record) => $record && $record->exists && $record->avatar),
                Forms\Components\Select::make('gender')
                    ->options([
                        'male' => '男',
                        'female' => '女',
                    ]),
                Forms\Components\TextInput::make('age')
                    ->numeric(),
                Forms\Components\TextInput::make('height')
                    ->maxLength(255),
                Forms\Components\TextInput::make('weight')
                    ->maxLength(255),
                Forms\Components\Textarea::make('hobbies')
                    ->rows(3),
                Forms\Components\Textarea::make('specialty')
                    ->rows(3),
                Forms\Components\Textarea::make('love_declaration')
                    ->rows(3),
                Forms\Components\TextInput::make('points')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
                Forms\Components\Toggle::make('is_admin')
                    ->label('管理员')
                    ->default(false),
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
                Tables\Columns\ImageColumn::make('avatar')
                    ->getStateUsing(fn ($record) => $record->avatar ? asset('storage/' . $record->avatar) : null)
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('gender')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'male' => '男',
                        'female' => '女',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('管理员')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => '男',
                        'female' => '女',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('是否激活'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
