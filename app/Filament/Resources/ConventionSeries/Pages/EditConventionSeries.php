<?php

namespace App\Filament\Resources\ConventionSeries\Pages;

use App\Filament\Resources\ConventionSeries\ConventionSeriesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConventionSeries extends EditRecord
{
    protected static string $resource = ConventionSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
