<?php

namespace App\Filament\Resources\Conventions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ConventionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('serie_id')
                    ->required()
                    ->numeric(),
                TextInput::make('anno')
                    ->required()
                    ->numeric(),
                TextInput::make('titolo_edizione')
                    ->required(),
                TextInput::make('codice')
                    ->required(),
                TextInput::make('italcon')
                    ->required()
                    ->numeric(),
                TextInput::make('city')
                    ->required(),
                DatePicker::make('date_start')
                    ->required(),
                DatePicker::make('date_end')
                    ->required(),
                DatePicker::make('premiazione'),
                Textarea::make('organizzazione')
                    ->columnSpanFull(),
                Textarea::make('ospiti')
                    ->columnSpanFull(),
                TextInput::make('toastmaster'),
                TextInput::make('votanti')
                    ->numeric(),
                TextInput::make('aventi_diritto')
                    ->numeric(),
                TextInput::make('url')
                    ->url(),
                TextInput::make('logo'),
            ]);
    }
}
