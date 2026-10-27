<?php

namespace App\Filament\Resources\Albos\Tables;

use App\Models\Albo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AlbosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('anno')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('categoria.nome')
                    ->label('Categoria')
                    ->sortable(),
                TextColumn::make('finalista')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state == 2 ? 'Vincitore' : 'Finalista')
                    ->color(fn ($state): string => $state == 2 ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('posizione')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('descrizione')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('note')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('anno', 'desc')
            ->filters([
                SelectFilter::make('anno')
                    ->options(fn (): array => Albo::query()->distinct()->orderByDesc('anno')->pluck('anno', 'anno')->all()),
                SelectFilter::make('categoria_id')
                    ->label('Categoria')
                    ->relationship('categoria', 'nome'),
                SelectFilter::make('finalista')
                    ->options(['1' => 'Finalista', '2' => 'Vincitore']),
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
