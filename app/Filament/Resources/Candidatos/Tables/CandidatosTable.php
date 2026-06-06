<?php

namespace App\Filament\Resources\Candidatos\Tables;

use App\Models\Annata;
use App\Models\Categoria;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CandidatosTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('categoria.nome_breve')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('anno')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('finalista')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('posizione')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('descrizione')
                    ->description(fn ($record) => new HtmlString('<a href="https://www.google.com/search?q='.urlencode($record->descrizione).'" target="_blank" style="text-decoration: underline;">Cerca su Google</a> - '.
                        ' <a href="https://www.amazon.it/s?i=stripbooks&k='.urlencode($record->descrizione).'" target="_blank" style="text-decoration: underline;">Cerca su Amazon</a> - '.
                        ' <a href="https://www.ibs.it/algolia-search?ts=as&qs=true&query='.urlencode($record->descrizione).'" target="_blank" style="text-decoration: underline;">Cerca su IBS</a>')
                    )
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('simili')
                    ->listWithLineBreaks()
                    ->html()
                    ->limitList(3),
                TextColumn::make('stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nuovo' => 'warning',
                        'valido' => 'success',
                        'escluso' => 'danger',
                    }),
                TextColumn::make('segnalazioni')
                    ->label('Voti')
                    ->sortable()
                    ->numeric(),
                TextColumn::make('spostatoIn.descrizione')
                    ->sortable()
                    ->visible(fn ($livewire): bool => ! ($livewire->tableFilters['nascondi_spostati']['isActive'] ?? false)),
                TextColumn::make('motivo_esclusione')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('verificato')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ordine')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('anno')
                    ->options(Annata::pluck('anno', 'anno'))
                    ->default(date('Y')),
                SelectFilter::make('categoria_id')
                    ->label('Categoria')
                    ->options(Categoria::active()->pluck('nome', 'id')),
                SelectFilter::make('stato')
                    ->options([
                        'nuovo' => 'Nuovo',
                        'valido' => 'Valido',
                        'escluso' => 'Escluso',
                    ]),
                SelectFilter::make('nascondi_spostati')
                    ->label('Mostra')
                    ->query(fn (Builder $query): Builder => $query->where('spostato_in', 0))
                    ->default(true)
                    ->options([
                        false => 'Spostati',
                        true => 'Validi',
                    ]),
            ], layout: FiltersLayout::AboveContent)
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
