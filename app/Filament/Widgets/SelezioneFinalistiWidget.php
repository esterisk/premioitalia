<?php

namespace App\Filament\Widgets;

use App\Calcolo\Nominees;
use App\Models\Annata;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Throwable;

class SelezioneFinalistiWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected string $view = 'filament.widgets.selezione-finalisti-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    /**
     * Esito dell'ultimo calcolo eseguito, per categoria.
     *
     * @var array<int, array{categoria: string, finalisti: array<int, string>, voti_minimi: int, media: float, candidati_validi: int, percentuale_votanti: int, categoria_valida: int}>
     */
    public array $risultati = [];

    public bool $dryRun = true;

    public static function canView(): bool
    {
        return Annata::corrente()?->fase() === 'spoglio1';
    }

    public function simulaSelezioneAction(): Action
    {
        return Action::make('simulaSelezione')
            ->label('Simula selezione')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->action(fn () => $this->eseguiSelezione(dryRun: true));
    }

    public function calcolaFinalistiAction(): Action
    {
        return Action::make('calcolaFinalisti')
            ->label('Calcola finalisti')
            ->icon('heroicon-o-trophy')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Calcola finalisti')
            ->modalDescription('I finalisti già registrati per l\'annata corrente verranno sostituiti e i conteggi ricalcolati. Confermi?')
            ->action(fn () => $this->eseguiSelezione(dryRun: false));
    }

    protected function eseguiSelezione(bool $dryRun): void
    {
        if (! Annata::corrente()) {
            Notification::make()->title('Nessuna annata corrente')->danger()->send();

            return;
        }

        try {
            $risultati = (new Nominees($dryRun))->calcolaFinalisti();
        } catch (Throwable $e) {
            report($e);

            Notification::make()
                ->title('Errore durante la selezione dei finalisti')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        $this->dryRun = $dryRun;
        $this->risultati = collect($risultati)
            ->map(fn (array $risultato): array => [
                'categoria' => $risultato['categoria']->nome,
                'finalisti' => collect($risultato['finalisti'])
                    ->map(fn ($candidato): string => $candidato->descrizione.' ('.$candidato->segnalazioni_count.')')
                    ->all(),
                'voti_minimi' => (int) $risultato['conteggio']->voti_minimi,
                'media' => round((float) $risultato['conteggio']->media, 2),
                'candidati_validi' => (int) $risultato['conteggio']->candidati_validi,
                'percentuale_votanti' => (int) $risultato['conteggio']->percentuale_votanti,
                'categoria_valida' => (int) $risultato['conteggio']->categoria_valida,
            ])
            ->all();

        $totaleFinalisti = collect($this->risultati)->sum(fn (array $r): int => count($r['finalisti']));

        Notification::make()
            ->title($dryRun
                ? "Simulazione completata: {$totaleFinalisti} finalisti su ".count($this->risultati).' categorie'
                : "Selezione completata: {$totaleFinalisti} finalisti su ".count($this->risultati).' categorie')
            ->success()
            ->send();
    }
}
