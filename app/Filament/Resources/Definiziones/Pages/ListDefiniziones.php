<?php

namespace App\Filament\Resources\Definiziones\Pages;

use App\Filament\Resources\Definiziones\DefinizioneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDefiniziones extends ListRecords
{
    protected static string $resource = DefinizioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
