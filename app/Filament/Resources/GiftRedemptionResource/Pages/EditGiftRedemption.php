<?php

namespace App\Filament\Resources\GiftRedemptionResource\Pages;

use App\Filament\Resources\GiftRedemptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGiftRedemption extends EditRecord
{
    protected static string $resource = GiftRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
