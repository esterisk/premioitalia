<?php

namespace App\Filament\Resources\ConventionSeries\Pages;

use App\Filament\Resources\ConventionSeries\ConventionSeriesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConventionSeries extends ListRecords
{
    protected static string $resource = ConventionSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
