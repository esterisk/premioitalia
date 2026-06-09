<?php

namespace App\Filament\Resources\Definiziones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DefinizioneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titolo')
                    ->required(),
                TextInput::make('variante'),
                Textarea::make('testo')
                    ->columnSpanFull(),
            ]);
    }
}
