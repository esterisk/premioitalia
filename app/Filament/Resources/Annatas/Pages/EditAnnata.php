<?php

namespace App\Filament\Resources\Annatas\Pages;

use App\Filament\Resources\Annatas\AnnataResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnata extends EditRecord
{
    protected static string $resource = AnnataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
