<?php

namespace App\Filament\Resources\Albos\Pages;

use App\Filament\Resources\Albos\AlboResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAlbos extends ListRecords
{
    protected static string $resource = AlboResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
