<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiftResource\Pages;
use App\Filament\Resources\GiftResource\RelationManagers;
use App\Models\Gift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GiftResource extends Resource
{
    protected static ?string $model = Gift::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = '礼物管理';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = '礼物';
    protected static ?string $pluralModelLabel = '礼物管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('礼物名称'),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'virtual' => '虚拟礼物',
                                'physical' => '实体礼物',
                            ])
                            ->default('virtual')
                            ->label('礼物类型'),
                        Forms\Components\Select::make('price_type')
                            ->required()
                            ->options([
                                'activity_points' => '活跃度',
                                'coins' => '金币',
                            ])
                            ->default('activity_points')
                            ->label('价格类型'),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->label('价格'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('详细信息')
                    ->schema([
                        Forms\Components\TextInput::make('image')
                            ->label('图片URL')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->label('上传图片')
                            ->imagePreviewHeight('150')
                            ->helperText('上传新图片会替换原有图片')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->minValue(1)
                            ->default(fn () => Gift::max('sort_order') + 1)
                            ->label('排序序号'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(3)
                            ->label('描述'),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->label('是否启用'),
                    ])->columns(2),
            ]);
    }

    public static function convertToThumbnailUrl($url): string
    {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            return $url;
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        
        if (str_ends_with($filename, '.th')) {
            return $url;
        }

        $dirname = dirname($path);

        $newPath = $dirname . '/' . $filename . '.th.' . $extension;

        return $parsed['scheme'] . '://' . $parsed['host'] . $newPath;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->label('排序序号'),
                Tables\Columns\ImageColumn::make('image')
                    ->label('图片'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('礼物名称'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'virtual' => '虚拟礼物',
                        'physical' => '实体礼物',
                        default => $state,
                    })
                    ->label('类型'),
                Tables\Columns\TextColumn::make('price_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'activity_points' => '活跃度',
                        'coins' => '金币',
                        default => $state,
                    })
                    ->label('价格类型'),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->label('价格'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('是否启用'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('创建时间'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('更新时间'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListGifts::route('/'),
            'create' => Pages\CreateGift::route('/create'),
            'edit' => Pages\EditGift::route('/{record}/edit'),
        ];
    }
}