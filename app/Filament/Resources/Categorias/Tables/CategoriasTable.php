<?php

namespace App\Filament\Resources\Categorias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Icons\Heroicon;


class CategoriasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->searchable(),
                TextColumn::make('nome_breve')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('supercategoria')
                    ->badge(),
                TextColumn::make('ordine')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('attiva')
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        '0' => Heroicon::OutlinedNoSymbol,
                        '1' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        default => 'gray',
                    })
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('campi')
                    ->searchable(),
                TextColumn::make('esclusi_ultimi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
