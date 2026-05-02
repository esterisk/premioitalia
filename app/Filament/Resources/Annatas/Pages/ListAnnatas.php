<?php

namespace App\Filament\Resources\Annatas\Pages;

use App\Filament\Resources\Annatas\AnnataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnnatas extends ListRecords
{
    protected static string $resource = AnnataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
