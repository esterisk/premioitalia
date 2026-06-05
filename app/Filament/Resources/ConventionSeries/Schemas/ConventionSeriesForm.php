<?php

namespace App\Filament\Resources\ConventionSeries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConventionSeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required(),
                TextInput::make('affiliata_da')
                    ->required()
                    ->numeric(),
                TextInput::make('sigla'),
            ]);
    }
}
