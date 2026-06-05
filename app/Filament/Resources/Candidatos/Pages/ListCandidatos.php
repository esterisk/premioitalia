<?php

namespace App\Filament\Resources\Candidatos\Pages;

use App\Filament\Resources\Candidatos\CandidatoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCandidatos extends ListRecords
{
    protected static string $resource = CandidatoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
