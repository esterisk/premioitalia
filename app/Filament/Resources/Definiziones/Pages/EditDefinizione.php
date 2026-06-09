<?php

namespace App\Filament\Resources\Definiziones\Pages;

use App\Filament\Resources\Definiziones\DefinizioneResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDefinizione extends EditRecord
{
    protected static string $resource = DefinizioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
