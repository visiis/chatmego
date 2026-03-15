<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['delete_avatar']) && $data['delete_avatar'] && $this->record->avatar) {
            Storage::disk('public')->delete($this->record->avatar);
            $data['avatar'] = null;
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->refresh();
    }
}
