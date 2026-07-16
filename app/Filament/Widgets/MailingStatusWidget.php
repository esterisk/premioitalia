<?php

namespace App\Filament\Widgets;

use App\Enums\MailingStatusEnum;
use App\Models\Annata;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;

class MailingStatusWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected string $view = 'filament.widgets.mailing-status-widget';

    protected static ?string $pollingInterval = '5s';

    protected function getViewData(): array
    {
        $annata = Annata::corrente();
        $raw = $annata?->mailing_status ?: 'idle';

        return [
            'mailingStatus' => MailingStatusEnum::tryFrom($raw),
        ];
    }

    public function startMailingAction(): Action
    {
        return Action::make('startMailing')
            ->label('Avvia mailing')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Avvia mailing')
            ->modalDescription('Confermi di voler avviare il mailing? Lo stato passerà in "Pronta".')
            ->action(function (): void {
                $annata = Annata::corrente();

                if (! $annata) {
                    Notification::make()->title('Nessuna annata corrente')->danger()->send();

                    return;
                }

                $annata->accessMailingStart();

                Notification::make()->title('Mailing avviato')->success()->send();
            });
    }

    public function stopMailingAction(): Action
    {
        return Action::make('stopMailing')
            ->label('Ferma mailing')
            ->icon('heroicon-o-stop-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Ferma mailing')
            ->modalDescription('Confermi di voler fermare il mailing? Lo stato tornerà in "In attesa".')
            ->action(function (): void {
                $annata = Annata::corrente();

                if (! $annata) {
                    Notification::make()->title('Nessuna annata corrente')->danger()->send();

                    return;
                }

                $annata->mailingStop();

                Notification::make()->title('Mailing fermato')->success()->send();
            });
    }

    public function sendReminderAction(): Action
    {
        return Action::make('sendReminder')
            ->label('Invia promemoria')
            ->icon('heroicon-o-bell-alert')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Invia promemoria')
            ->modalDescription('Confermi di voler inviare il sollecito agli elettori che non hanno ancora inviato il voto?')
            ->action(function (): void {
                $annata = Annata::corrente();

                if (! $annata) {
                    Notification::make()->title('Nessuna annata corrente')->danger()->send();

                    return;
                }

                $annata->mailingSollecito();

                Notification::make()->title('Promemoria inviati')->success()->send();
            });
    }
}
