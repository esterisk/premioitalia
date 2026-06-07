<?php

namespace App\Filament\Resources\Candidatos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Toggle;

class CandidatoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('categoria_id')
                    ->disabled()
                    ->numeric()
                    ->default(0),
                TextInput::make('anno')
                    ->disabled()
                    ->numeric(),
                KeyValue::make('campi')
                    ->required()
                    ->addActionLabel('aggiungi campo')
                    ->columnSpanFull(),
                Select::make('stato')
                    ->disabled()
                    ->options(['valido' => 'Valido', 'escluso' => 'Escluso', 'spostato' => 'Spostato'])
                    ->default('valido'),
                TextInput::make('motivo_esclusione'),
                Toggle::make('verificato')
                    ->required()
                    ->default(false),
                TextInput::make('identificativo')
                    ->disabled(),
                /*
                TextInput::make('ordine')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('spostato_in')
                    ->numeric()
                    ->default(0),
                TextInput::make('immagine'),
                TextInput::make('link_testo'),
                TextInput::make('link_immagine'),
                */
            ]);
    }
}
