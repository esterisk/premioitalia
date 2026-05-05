<?php

namespace App\Filament\Resources\Candidaturas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CandidaturaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('categoria_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('anno')
                    ->numeric(),
                TextInput::make('descrizione')
                    ->required(),
                Textarea::make('campi')
                    ->required()
                    ->columnSpanFull(),
                Select::make('stato')
                    ->options(['nuovo' => 'Nuovo', 'valido' => 'Valido', 'escluso' => 'Escluso'])
                    ->default('valido'),
                TextInput::make('motivo_esclusione'),
                TextInput::make('identificativo')
                    ->required(),
            ]);
    }
}
