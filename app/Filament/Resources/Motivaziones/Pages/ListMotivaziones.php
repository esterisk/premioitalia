<?php

namespace App\Filament\Resources\Motivaziones\Pages;

use App\Filament\Resources\Motivaziones\MotivazioneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMotivaziones extends ListRecords
{
    protected static string $resource = MotivazioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
