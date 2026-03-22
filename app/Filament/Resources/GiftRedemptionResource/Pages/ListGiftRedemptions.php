<?php

namespace App\Filament\Resources\GiftRedemptionResource\Pages;

use App\Filament\Resources\GiftRedemptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGiftRedemptions extends ListRecords
{
    protected static string $resource = GiftRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
