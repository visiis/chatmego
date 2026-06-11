<?php

namespace App\Filament\Resources\UserAlbumResource\Pages;

use App\Filament\Resources\UserAlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserAlbums extends ListRecords
{
    protected static string $resource = UserAlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
