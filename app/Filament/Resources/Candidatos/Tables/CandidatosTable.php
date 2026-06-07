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
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use App\Models\Candidato;
use Filament\Tables\Enums\RecordActionsPosition;
use App\Models\Motivazione;

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
                    //->wrap()
                    ->width('30%')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('simili')
                    ->listWithLineBreaks()
                    ->width('20%')
                    ->html()
                    ->limitList(3),
                TextColumn::make('stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'spostato' => 'warning',
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        'valido' => 'Valido',
                        'spostato' => 'Spostato',
                        'escluso' => 'Escluso',
                    ])->default('valido'),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('minuscole')
                    ->label('Aa')
                    // ->icon('heroicon-o-information-circle')
                    ->action(function ($record) {
                        $record->minuscole()->save();
                    })
                    ->color(fn ($record) => ! $record->needLowering() ? 'gray' : 'primary')
                    ->disabled(fn ($record) => ! $record->needLowering()),
                Action::make('escludi')
                    ->label('')
                    ->icon('heroicon-s-hand-thumb-down')
                    ->color(fn ($record) => $record->stato == 'escluso' ? 'danger' : 'primary')
                    ->schema([
                        Select::make('motivazione_id')
                            ->label('Motivazione')
                            ->options(Motivazione::pluck('motivazione', 'id'))
                            ->required(),
                    ])
                    ->action(function (array $data, Candidato $record): void {
                        $record->escludi($data['motivazione_id']);
                    })
                    ->disabled(fn ($record) => $record->stato == 'escluso'),
                Action::make('conferma')
                    ->label('')
                    ->icon('heroicon-s-hand-thumb-up')
                    ->color(fn ($record) => $record->verificato == 0 ? 'primary' : 'success')
                    ->action(function ($record) {
                        if ($record->stato != 'valido') {
                            $record->riammetti();
                        }
                        $record->verificato = 1 - $record->verificato;
                        $record->save();
                    }),
                Action::make('sposta')
                    ->label('')->tooltip('Unisci ad altro candidato')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('warning')
                    ->action(function (Candidato $record, array $data) {
                        $record->spostaIn($data['spostato_in']);
                    })
                    ->form([
                        Select::make('spostato_in')
                            ->label('Candidato di destinazione')
                            ->options(fn (Candidato $record) => $record->similiOptions(20))
                            ->required(),
                    ]),
                EditAction::make()->tooltip('Modifica record')->label(''),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
