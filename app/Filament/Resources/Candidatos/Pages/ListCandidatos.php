<?php

namespace App\Filament\Resources\Candidatos\Pages;

use App\Filament\Resources\Candidatos\CandidatoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\Candidato;

class ListCandidatos extends ListRecords
{
    protected static string $resource = CandidatoResource::class;

    public function mount(): void
    {
        parent::mount();

        Candidato::updateSegnalazioni(null);
    }


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
