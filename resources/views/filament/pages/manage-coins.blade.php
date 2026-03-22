<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            添加金币
        </x-slot>

        <x-slot name="description">
            为用户手动添加金币（1 元人民币 = 10 金币）
        </x-slot>

        <form wire:submit="addCoins" class="space-y-4">
            {{ $this->form }}

            <div class="flex items-center gap-4">
                <x-filament::button type="submit">
                    添加金币
                </x-filament::button>

                <x-filament::button type="button" color="gray" wire:click="$refresh">
                    重置
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <x-filament::section class="mt-6">
        <x-slot name="heading">
            快速操作
        </x-slot>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-filament::button color="success" wire:click="quickAdd(100)">
                💰 +100 金币
            </x-filament::button>

            <x-filament::button color="primary" wire:click="quickAdd(500)">
                💰 +500 金币
            </x-filament::button>

            <x-filament::button color="warning" wire:click="quickAdd(1000)">
                💰 +1000 金币
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament::panels::page>
