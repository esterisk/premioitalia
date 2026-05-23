<?php

namespace App\Filament\Resources\Candidaturas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\KeyValue;
use Illuminate\Database\Eloquent\Builder;

class CandidaturaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('categoria_id')
                    ->required()
                    ->relationship(
                        name: 'categoria',
                        titleAttribute: 'nome',
                        modifyQueryUsing: fn (Builder $query): Builder => $query->active(),
                    ),
                TextInput::make('anno')
                    ->numeric(),
                KeyValue::make('campi')
                    ->required()
                    ->addActionLabel('aggiungi campo')
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
