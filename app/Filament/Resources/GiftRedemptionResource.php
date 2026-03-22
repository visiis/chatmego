<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiftRedemptionResource\Pages;
use App\Filament\Resources\GiftRedemptionResource\RelationManagers;
use App\Models\GiftRedemption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GiftRedemptionResource extends Resource
{
    protected static ?string $model = GiftRedemption::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = '礼物管理';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = '兑换记录';
    protected static ?string $pluralModelLabel = '礼物兑换记录';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('兑换信息')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('用户'),
                        Forms\Components\Select::make('gift_id')
                            ->relationship('gift', 'name')
                            ->required()
                            ->label('礼物'),
                        Forms\Components\Select::make('user_gift_id')
                            ->relationship('userGift', 'id')
                            ->required()
                            ->label('用户礼物 ID'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('收货信息')
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->required()
                            ->maxLength(255)
                            ->label('收件人姓名'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->label('电话'),
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->rows(3)
                            ->label('地址'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('状态')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => '待处理',
                                'shipped' => '已发货',
                                'delivered' => '已送达',
                            ])
                            ->default('pending')
                            ->label('兑换状态'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->label('用户'),
                Tables\Columns\TextColumn::make('gift.name')
                    ->searchable()
                    ->label('礼物'),
                Tables\Columns\TextColumn::make('quantity')
                    ->badge()
                    ->color('info')
                    ->label('数量'),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->searchable()
                    ->label('收件人'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('电话'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => '待处理',
                        'processing' => '处理中',
                        'shipped' => '已发货',
                        'completed' => '已完成',
                        'cancelled' => '已取消',
                        default => $state,
                    })
                    ->label('状态'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('创建时间'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => '待处理',
                        'shipped' => '已发货',
                        'delivered' => '已送达',
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGiftRedemptions::route('/'),
            'create' => Pages\CreateGiftRedemption::route('/create'),
            'edit' => Pages\EditGiftRedemption::route('/{record}/edit'),
        ];
    }
}
