<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\CoinTransaction;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ManageCoins extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string $view = 'filament.pages.manage-coins';
    protected static ?string $navigationGroup = '金币管理';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = '金币管理';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('选择用户')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('coins')
                    ->label('金币数量')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->prefix('💰')
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->label('备注')
                    ->placeholder('说明金币添加原因')
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function addCoins(): void
    {
        $data = $this->form->getState();
        
        $user = User::find($data['user_id']);
        
        if (!$user) {
            Notification::make()
                ->title('用户不存在')
                ->danger()
                ->send();
            return;
        }

        $coins = (int) $data['coins'];
        $user->increment('coins', $coins);
        $user->increment('total_coins_recharged', $coins);

        // 记录交易历史
        CoinTransaction::create([
            'user_id' => $user->id,
            'amount' => $coins,
            'balance_after' => $user->coins,
            'type' => 'add',
            'note' => $data['note'] ?? null,
            'admin_id' => auth()->id(),
        ]);

        Notification::make()
            ->title('金币添加成功')
            ->body("已为用户 {$user->name} 添加 {$coins} 金币")
            ->success()
            ->send();

        $this->form->fill();
    }

    public function quickAdd(int $amount): void
    {
        $data = $this->form->getState();
        
        if (empty($data['user_id'])) {
            Notification::make()
                ->title('请先选择用户')
                ->warning()
                ->send();
            return;
        }

        $user = User::find($data['user_id']);
        
        if (!$user) {
            Notification::make()
                ->title('用户不存在')
                ->danger()
                ->send();
            return;
        }

        $user->increment('coins', $amount);
        $user->increment('total_coins_recharged', $amount);

        // 记录交易历史
        CoinTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'balance_after' => $user->coins,
            'type' => 'add',
            'note' => $data['note'] ?? "快速添加 {$amount} 金币",
            'admin_id' => auth()->id(),
        ]);

        Notification::make()
            ->title("快速添加成功")
            ->body("已为用户 {$user->name} 添加 {$amount} 金币")
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CoinTransaction::query()
                    ->with(['user', 'admin'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('用户')->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('金币变化')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '') . $state)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label('操作后余额')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->formatStateUsing(fn ($state) => [
                        'add' => '添加',
                        'deduct' => '扣除',
                        'recharge' => '充值',
                        'exchange' => '兑换',
                    ][$state] ?? $state),
                Tables\Columns\TextColumn::make('admin.name')->label('操作管理员')->searchable(),
                Tables\Columns\TextColumn::make('note')->label('备注')->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('操作时间')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'add' => '添加',
                        'deduct' => '扣除',
                        'recharge' => '充值',
                        'exchange' => '兑换',
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name'),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(15);
    }
}
