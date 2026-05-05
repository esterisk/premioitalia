<?php

namespace App\Filament\Resources\Candidaturas\Pages;

use App\Filament\Resources\Candidaturas\CandidaturaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCandidaturas extends ListRecords
{
    protected static string $resource = CandidaturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
