<?php

namespace App\Filament\Resources\Candidatos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CandidatoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('spostato_in')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('categoria_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('anno')
                    ->numeric(),
                TextInput::make('finalista')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('posizione')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('descrizione')
                    ->required(),
                Textarea::make('campi')
                    ->required()
                    ->columnSpanFull(),
                Select::make('stato')
                    ->options(['valido' => 'Valido', 'escluso' => 'Escluso', 'spostato' => 'Spostato'])
                    ->default('valido'),
                TextInput::make('motivo_esclusione'),
                TextInput::make('verificato')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('identificativo')
                    ->required(),
                TextInput::make('ordine')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('immagine'),
                TextInput::make('link_testo'),
                TextInput::make('link_immagine'),
            ]);
    }
}
