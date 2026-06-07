<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                IconColumn::make('user_status')
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
                TextColumn::make('status_detail')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ultimo_voto')
                    ->alignCenter()
                    ->formatStateUsing(fn (?string $state): string => (intval($state) == 0 ? 'N/A' : $state))
                    ->color(fn (string $state): string => match (true) {
                        $state === 'N/A' => 'gray',
                        $state === '0' => 'gray',
                        (int) $state >= (int) date('Y') - 2 => 'success',
                        (int) $state >= (int) date('Y') - 5 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('last_invitation')
                    ->searchable(),
                TextColumn::make('invitation_sent')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('email_errors')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('invitation_open')
                    ->searchable(),
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
                Action::make('send_access')
                    ->label('Invia accesso')
                    ->icon(Heroicon::PaperAirplane)
                    ->color('primary')
                    ->action(fn ($record) => $record->sendAccess()),
                Action::make('set_password')
                    ->label('Imposta password')
                    ->icon(Heroicon::LockClosed)
                    ->color('warning')
                    ->modalHeading('Imposta password')
                    ->schema([
                        TextInput::make('password')
                            ->label('Nuova password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8),
                        TextInput::make('password_confirmation')
                            ->label('Conferma password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password'),
                    ])
                    ->action(function (array $data, User $record): void {
                        $record->update(['password' => Hash::make($data['password'])]);

                        Notification::make()
                            ->title('Password aggiornata')
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
