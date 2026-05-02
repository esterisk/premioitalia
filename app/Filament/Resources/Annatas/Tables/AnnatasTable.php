<?php

namespace App\Filament\Resources\Annatas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;

class AnnatasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('anno')
                    ->sortable(),
                TextColumn::make('candidature_da')
                    ->date()
                    ->sortable(),
                TextColumn::make('candidature_a')
                    ->date()
                    ->sortable(),
                TextColumn::make('fase_1_da')
                    ->date()
                    ->sortable(),
                TextColumn::make('fase_1_a')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fase_2_da')
                    ->date()
                    ->sortable(),
                TextColumn::make('fase_2_a')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('finalisti')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('premiazione')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('finalisti_pubblici')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('risultati_pubblici')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mailing_status')
                    ->searchable(),
            ])
            ->defaultSort('anno', direction: 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('corrente')
                    ->label('Corrente')
                    ->button()
                    ->action(function ($record) {
                        $record->newQuery()->update(['corrente' => 0]);
                        $record->corrente = 1;
                        $record->save();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
