<?php

namespace App\Filament\Resources\Albos\Pages;

use App\Filament\Resources\Albos\AlboResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAlbo extends CreateRecord
{
    protected static string $resource = AlboResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['campi'] = json_encode($data['campi'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $data;
    }
}
