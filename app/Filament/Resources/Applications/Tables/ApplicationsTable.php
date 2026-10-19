<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Enums\StatoApplicationEnum;
use App\Models\Application;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Ricevuta')->dateTime()->sortable(),
                TextColumn::make('name')->label('Nome')->searchable(['first_name', 'last_name']),
                TextColumn::make('email')->searchable(),
                TextColumn::make('convention.titolo_edizione')->label('Convention')->toggleable(),
                IconColumn::make('location_confirmed')->label('Posizione')->boolean(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->formatStateUsing(fn (StatoApplicationEnum $state): string => $state->label())
                    ->color(fn (StatoApplicationEnum $state): string => match ($state) {
                        StatoApplicationEnum::Ratificata => 'success',
                        StatoApplicationEnum::DaVerificare => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('ip_address')->label('IP')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(collect(StatoApplicationEnum::cases())->mapWithKeys(fn (StatoApplicationEnum $stato) => [$stato->value => $stato->label()])),
            ])
            ->recordActions([
                Action::make('ratifica')
                    ->label('Ratifica')
                    ->icon(Heroicon::CheckBadge)
                    ->color('success')
                    ->visible(fn (Application $record): bool => $record->isRatificabile())
                    ->requiresConfirmation()
                    ->modalHeading('Ratifica richiesta')
                    ->modalDescription(fn (Application $record): string => "{$record->name} verrà inserito fra gli elettori e riceverà una email di benvenuto.")
                    ->action(function (Application $record): void {
                        $record->ratifica();

                        Notification::make()
                            ->title('Richiesta ratificata, email di benvenuto inviata')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
