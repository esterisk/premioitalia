<?php

namespace App\Filament\Resources\Albos\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AlboForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('categoria_id')
                    ->label('Categoria')
                    ->relationship('categoria', 'nome')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('anno')
                    ->required()
                    ->numeric(),
                Select::make('finalista')
                    ->options(['1' => 'Finalista', '2' => 'Vincitore'])
                    ->default(1)
                    ->required(),
                Select::make('posizione')
                    ->options([
                        '0' => 'Nessuna',
                        '1' => 'Primo',
                        '2' => 'Secondo',
                        '3' => 'Terzo',
                    ])
                    ->default(0)
                    ->helperText('Posizione sul podio (1 = vincitore, 2 = secondo, 3 = terzo).'),
                TextInput::make('descrizione')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                KeyValue::make('campi')
                    ->addActionLabel('aggiungi campo')
                    ->columnSpanFull(),
                TextInput::make('note')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
