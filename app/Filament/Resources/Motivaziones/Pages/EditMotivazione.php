<?php

namespace App\Filament\Resources\Motivaziones\Pages;

use App\Filament\Resources\Motivaziones\MotivazioneResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMotivazione extends EditRecord
{
    protected static string $resource = MotivazioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
