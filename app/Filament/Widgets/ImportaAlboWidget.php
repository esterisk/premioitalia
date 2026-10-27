<?php

namespace App\Filament\Widgets;

use App\Models\Albo;
use App\Models\Annata;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;

class ImportaAlboWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected string $view = 'filament.widgets.importa-albo-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $annata = Annata::corrente();

        return $annata?->fase() === 'post'
            && (int) Albo::max('anno') < (int) $annata->anno;
    }

    public function importaAlboAction(): Action
    {
        return Action::make('importaAlbo')
            ->label('Importa nell\'albo')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Importa risultati nell\'albo')
            ->modalDescription('I finalisti e i vincitori dell\'annata corrente verranno copiati nell\'albo d\'oro. Confermi?')
            ->action(function (): void {
                if (! static::canView()) {
                    return;
                }

                $risultato = Albo::consolida(Annata::corrente());

                if ($risultato['status'] !== 'success') {
                    Notification::make()
                        ->title('Importazione non riuscita')
                        ->body($risultato['message'] ?? null)
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title("Importate {$risultato['inserted']} voci nell'albo")
                    ->success()
                    ->send();

                $this->dispatch('$refresh');
            });
    }
}
