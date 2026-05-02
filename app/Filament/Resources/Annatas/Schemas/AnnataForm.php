<?php

namespace App\Filament\Resources\Annatas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnnataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('anno')
                    ->required()
                    ->numeric(),
                DatePicker::make('candidature_da')
                    ->required(),
                DatePicker::make('candidature_a')
                    ->required(),
                DatePicker::make('fase_1_da')
                    ->required(),
                DatePicker::make('fase_1_a')
                    ->required(),
                DatePicker::make('fase_2_da')
                    ->required(),
                DatePicker::make('fase_2_a')
                    ->required(),
                DatePicker::make('finalisti'),
                DatePicker::make('premiazione')
                    ->required(),
                TextInput::make('finalisti_pubblici')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('risultati_pubblici')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('mailing_status')
                    ->required()
                    ->default('idle'),
            ]);
    }
}
