<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAlbumResource\Pages;
use App\Filament\Resources\UserAlbumResource\RelationManagers;
use App\Models\UserAlbum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserAlbumResource extends Resource
{
    protected static ?string $model = UserAlbum::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = '内容管理';

    protected static ?string $label = '相册';

    protected static ?string $pluralLabel = '相册管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('用户')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('相册名称')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('相册描述'),
                Forms\Components\Toggle::make('privacy')
                    ->label('隐私设置')
                    ->helperText('开启=公开，关闭=隐藏（需要付费）'),
                Forms\Components\TextInput::make('price')
                    ->label('观看价格（金币）')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn ($get) => !$get('privacy')),
                Forms\Components\Toggle::make('status')
                    ->label('状态')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('用户')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('相册名称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('privacy')
                    ->label('隐私设置')
                    ->formatStateUsing(fn ($state) => $state ? '公开' : '隐藏'),
                Tables\Columns\TextColumn::make('price')
                    ->label('价格')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . ' 金币' : '免费'),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('浏览次数'),
                Tables\Columns\TextColumn::make('purchase_count')
                    ->label('购买次数'),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn ($state) => $state ? '启用' : '禁用'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('privacy')
                    ->label('隐私设置')
                    ->options([
                        1 => '公开',
                        0 => '隐藏',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        1 => '启用',
                        0 => '禁用',
                    ]),
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
            RelationManagers\PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserAlbums::route('/'),
            'create' => Pages\CreateUserAlbum::route('/create'),
            'edit' => Pages\EditUserAlbum::route('/{record}/edit'),
        ];
    }
}
