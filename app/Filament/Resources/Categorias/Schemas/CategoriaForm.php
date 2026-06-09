<?php

namespace App\Filament\Resources\Categorias\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;

class CategoriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required(),
                TextInput::make('nome_breve'),
                TextInput::make('slug')
                    ->required(),
                Select::make('supercategoria')
                    ->options([
            'Opere' => 'Opere',
            'Produzioni editoriali' => 'Produzioni editoriali',
            'Personalità' => 'Personalità',
            'Categorie non ufficiali' => 'Categorie non ufficiali',
            'Premi speciali' => 'Premi speciali',
        ]),
                TextInput::make('ordine')
                    ->required()
                    ->numeric(),
                Toggle::make('attiva')
                    ->required(),
                Textarea::make('definizione')
                    ->columnSpanFull(),
                TextInput::make('campi')
                    ->required(),
                TextInput::make('esclusi_ultimi')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
