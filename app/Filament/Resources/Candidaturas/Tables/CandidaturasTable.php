<?php

namespace App\Filament\Resources\Candidaturas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use App\Models\Candidatura;
use App\Models\Motivazione;

class CandidaturasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('categoria.nome')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('anno')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('descrizione')
                    ->description(fn ($record) => new HtmlString('<a href="https://www.google.com/search?q=' . urlencode($record->descrizione) . '" target="_blank" style="text-decoration: underline;">Cerca su Google</a> - '.
                        ' <a href="https://www.amazon.it/s?i=stripbooks&k=' . urlencode($record->descrizione) . '" target="_blank" style="text-decoration: underline;">Cerca su Amazon</a> - '.
                        ' <a href="https://www.ibs.it/algolia-search?ts=as&qs=true&query=' . urlencode($record->descrizione) . '" target="_blank" style="text-decoration: underline;">Cerca su IBS</a>')
                    )
                    ->searchable(),
                TextColumn::make('stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nuovo' => 'warning',
                        'valido' => 'success',
                        'escluso' => 'danger',
                    }),
                TextColumn::make('motivo_esclusione')
                    ->searchable()
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
                    ->options(\App\Models\Annata::pluck('anno', 'anno'))
                    ->default(date('Y')),
                SelectFilter::make('categoria_id')
                    ->options(\App\Models\Categoria::pluck('nome', 'id')),
                SelectFilter::make('stato')
                    ->options([
                        'nuovo' => 'Nuovo',
                        'valido' => 'Valido',
                        'escluso' => 'Escluso',
                    ])
                ])
            ->recordActions([
                Action::make('escludi')
                    ->label('Escludi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->schema([
                        Select::make('motivazione_id')
                                ->label('Motivazione')
                                ->options(Motivazione::pluck('motivazione', 'id'))
                                ->required(),
                        ])
                    ->action(function (array $data, Candidatura $record): void {
                            $record->motivazione()->associate($data['motivazione_id']);
                            $record->motivo_esclusione = Motivazione::find($data['motivazione_id'])->motivazione;
                            $record->stato = 'escluso';
                            $record->save();
                    })
                    ->visible(fn ($record) => $record->stato !== 'escluso'),
                Action::make('accetta')
                    ->label('Accetta')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->stato = 'valido';
                        $record->save();
                    })
                    ->visible(fn ($record) => $record->stato !== 'valido'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
