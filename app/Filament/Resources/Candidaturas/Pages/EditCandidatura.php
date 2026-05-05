<?php

namespace App\Filament\Resources\Candidaturas\Pages;

use App\Filament\Resources\Candidaturas\CandidaturaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCandidatura extends EditRecord
{
    protected static string $resource = CandidaturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
