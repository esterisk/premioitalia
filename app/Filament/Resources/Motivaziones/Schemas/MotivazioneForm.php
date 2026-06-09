<?php

namespace App\Filament\Resources\Motivaziones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MotivazioneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('motivazione')
                    ->required(),
                TextInput::make('breve')
                    ->required(),
            ]);
    }
}
