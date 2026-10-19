<?php

namespace App\Filament\Resources\Applications\Schemas;

use App\Enums\StatoApplicationEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')->label('Nome')->required(),
                TextInput::make('last_name')->label('Cognome')->required(),
                TextInput::make('email')->email()->required(),
                Select::make('convention_id')
                    ->label('Convention')
                    ->relationship('convention', 'titolo_edizione'),
                Select::make('status')
                    ->label('Stato')
                    ->options(collect(StatoApplicationEnum::cases())->mapWithKeys(fn (StatoApplicationEnum $stato) => [$stato->value => $stato->label()]))
                    ->required(),
                Textarea::make('notes')->label('Note')->columnSpanFull(),
                TextInput::make('ip_address')->label('IP')->disabled(),
                TextInput::make('user_agent')->label('Browser')->disabled(),
            ]);
    }
}
