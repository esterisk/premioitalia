<?php

namespace App\Filament\Resources\Conventions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class ConventionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('serie_id')
                    ->relationship('serie', 'nome')
                    ->required(),
                TextInput::make('anno')
                    ->required()
                    ->numeric(),
                TextInput::make('titolo_edizione')
                    ->required(),
                    /*
                TextInput::make('codice')
                    ->required(),
                */
                TextInput::make('city')
                    ->required(),
                DatePicker::make('date_start')
                    ->required(),
                DatePicker::make('date_end')
                    ->required(),
                TextInput::make('italcon')
                    ->numeric(),
                DatePicker::make('premiazione'),
                Textarea::make('organizzazione')
                    ->columnSpanFull(),
                Textarea::make('ospiti')
                    ->columnSpanFull(),
                TextInput::make('toastmaster'),
                /*
                TextInput::make('votanti')
                    ->numeric(),
                TextInput::make('aventi_diritto')
                    ->numeric(),
                */
                TextInput::make('url')
                    ->url(),
                TextInput::make('logo'),
            ]);
    }
}
