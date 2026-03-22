<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class ManageCoins extends Page implements HasForms
{
    use InteractsWithForms;

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
                    ->minValue(1)
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

        Notification::make()
            ->title("快速添加成功")
            ->body("已为用户 {$user->name} 添加 {$amount} 金币")
            ->success()
            ->send();
    }
}
