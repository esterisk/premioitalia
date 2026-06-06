<?php

namespace App\Filament\Widgets;

use App\Models\Convention;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use App\Models\Invitation;
use Filament\Notifications\Notification;

class InvitationWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected string $view = 'filament.widgets.invitation-widget';

    // protected int|string|array $columnSpan = 'full';

    public string $testo = '';

    public ?int $conventionId = null;

    protected function getViewData(): array
    {
        return [
            'conventions' => Convention::validePerVoto()->orderByDesc('anno')->pluck('titolo_edizione', 'id'),
        ];
    }

    public function mandaInvitoAction(): Action
    {
        return Action::make('mandaInvito')
            ->label('Manda invito')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->action(function (): void {
                $result = Invitation::CreateFromText($this->testo, $this->conventionId);
                $message = '';
                $errors = 0;
                $success = 0;
                $color = '';

                if (!$this->conventionId) {
                    $message = 'Errore: Selezionare una convention';
                    Notification::make()
                        ->title($message)
                        ->send();
                    return;
                }

                if (!$this->testo) {
                    $message = 'Errore: Inserire un testo';
                    Notification::make()
                        ->title($message)
                        ->send();
                    return;
                }

                foreach ($result as $line => $outcome) {
                    if (strpos($outcome, 'Errore') === 0) {
                        $errors++;
                        $color = 'red';
                    } else {
                        $success++;
                        $color = 'green';
                    }
                    $message .= "<b style=\"color:$color;\">$line:</b> $outcome<br>";

                }

                Notification::make()
                    ->title($message)
                    ->send();

                $this->testo = '';
                $this->conventionId = null;
            });
    }
}
