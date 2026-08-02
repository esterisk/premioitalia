<?php

namespace App\Filament\Pages;

use App\Calcolo\Australian;
use App\Calcolo\ElaborazioneAustralian;
use App\Models\Annata;
use App\Models\Categoria;
use App\Models\ConteggioFinale;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Throwable;
use UnitEnum;

class RisultatiVotazione extends Page
{
    protected string $view = 'filament.pages.risultati-votazione';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'FASE DUE';

    protected static ?string $navigationLabel = 'Risultati votazione';

    protected static ?string $title = 'Risultati votazione';

    /**
     * Classi di colore assegnate ai candidati, nell'ordine dei finalisti.
     *
     * @var array<int, string>
     */
    public const COLORI = [
        'bg-amber-500',
        'bg-sky-500',
        'bg-emerald-500',
        'bg-violet-500',
        'bg-rose-500',
        'bg-cyan-500',
        'bg-lime-500',
        'bg-fuchsia-500',
        'bg-orange-500',
        'bg-teal-500',
    ];

    public ?int $categoriaId = null;

    public function mount(): void
    {
        $this->categoriaId ??= $this->categorie()->first()?->getKey();
    }

    /**
     * @return Collection<int, Categoria>
     */
    public function categorie(): Collection
    {
        return Categoria::attive();
    }

    public function getSubheading(): ?string
    {
        $annata = Annata::corrente();

        if (! $annata) {
            return null;
        }

        return 'Annata '.$annata->anno.': conteggio australian ballot sulle preferenze di seconda fase.';
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('ricalcola')
                ->label('Ricalcola')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('gray')
                ->action(fn () => null),
            Action::make('salvaCategoria')
                ->label('Salva questa categoria')
                ->icon(Heroicon::OutlinedTrophy)
                ->color('primary')
                ->visible(fn (): bool => $this->categoriaId !== null)
                ->requiresConfirmation()
                ->modalHeading('Salva i risultati della categoria')
                ->modalDescription('Il conteggio finale viene salvato e i candidati della categoria aggiornati con vincitore e podio, sostituendo i risultati salvati in precedenza.')
                ->action(fn () => $this->salva($this->categoriaId)),
            Action::make('salvaTutte')
                ->label('Salva tutte le categorie')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Chiudi la votazione')
                ->modalDescription('Vengono calcolati e salvati i risultati di tutte le categorie attive, sostituendo quelli già salvati. Confermi?')
                ->action(fn () => $this->salva(null)),
        ];
    }

    protected function salva(?int $categoriaId): void
    {
        $calcolo = new Australian;

        try {
            if ($categoriaId) {
                $calcolo->calcolaCategoria($categoriaId, save: true);
                $messaggio = 'Risultati salvati per la categoria selezionata.';
            } else {
                $calcolo->calcolaVincitori(true);
                $messaggio = 'Risultati salvati per tutte le categorie attive.';
            }
        } catch (Throwable $e) {
            report($e);

            Notification::make()
                ->title('Errore durante il salvataggio dei risultati')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        Notification::make()->title($messaggio)->success()->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $annata = Annata::corrente();
        $categorie = $this->categorie();
        $categoria = $categorie->firstWhere('id', $this->categoriaId);

        return [
            'annata' => $annata,
            'categorie' => $categorie,
            'categoria' => $categoria,
            'risultato' => ($annata && $categoria) ? $this->elabora($annata, $categoria) : null,
        ];
    }

    /**
     * Ricalcola la categoria senza salvare nulla e prepara i dati per la vista.
     *
     * @return array<string, mixed>
     */
    protected function elabora(Annata $annata, Categoria $categoria): array
    {
        $salvato = ConteggioFinale::whereAnno($annata->anno)
            ->whereCategoriaId($categoria->getKey())
            ->first();

        $calcolo = new Australian;

        try {
            $conteggio = $calcolo->calcolaCategoria($categoria);
        } catch (Throwable $e) {
            report($e);

            return ['errore' => $e->getMessage(), 'salvato' => $salvato];
        }

        $indicatori = $calcolo->indicatori($calcolo->candidati);
        $vincitori = $this->esplodi($conteggio->vincitori);
        $secondi = $this->esplodi($conteggio->secondi);
        $terzi = $this->esplodi($conteggio->terzi);

        return [
            'errore' => null,
            'conteggio' => $conteggio,
            'indicatori' => $indicatori,
            'conti' => $calcolo->conti_candidati,
            'finalisti' => collect($calcolo->candidati)->all(),
            'vincitori' => $vincitori,
            'secondi' => $secondi,
            'terzi' => $terzi,
            'fasi' => array_values(array_filter([
                $this->fase('Elaborazione vincitore', $conteggio->elaborazione, $vincitori, $indicatori),
                $this->fase('Elaborazione secondo posto', $conteggio->elaborazione_2, $secondi, $indicatori),
                $this->fase('Elaborazione terzo posto', $conteggio->elaborazione_3, $terzi, $indicatori),
            ])),
            'salvato' => $salvato,
            'allineato' => $salvato !== null && $this->coincide($salvato, $conteggio),
        ];
    }

    /**
     * @param  array<int, int>  $assegnati
     * @param  array<int|string, array{d: string, c: int, s: string}>  $indicatori
     * @return array<string, mixed>|null
     */
    protected function fase(string $titolo, ?string $elaborazione, array $assegnati, array $indicatori): ?array
    {
        $turni = ElaborazioneAustralian::turni($elaborazione, $assegnati, $indicatori);

        if (! $turni) {
            return null;
        }

        return [
            'titolo' => $titolo,
            'turni' => $turni,
            'assegnati' => $assegnati,
        ];
    }

    /**
     * @return array<int, int>
     */
    protected function esplodi(?string $lista): array
    {
        if (blank($lista)) {
            return [];
        }

        return array_map('intval', array_filter(explode(',', $lista), fn (string $id): bool => $id !== ''));
    }

    protected function coincide(ConteggioFinale $salvato, ConteggioFinale $calcolato): bool
    {
        foreach (['vincitori', 'secondi', 'terzi', 'elenco_finalisti'] as $campo) {
            if ((string) $salvato->{$campo} !== (string) $calcolato->{$campo}) {
                return false;
            }
        }

        return true;
    }
}
