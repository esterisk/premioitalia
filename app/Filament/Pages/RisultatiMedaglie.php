<?php

namespace App\Filament\Pages;

use App\Calcolo\Australian;
use App\Calcolo\Medaglie;
use App\Models\Annata;
use App\Models\Categoria;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Throwable;
use UnitEnum;

class RisultatiMedaglie extends Page
{
    protected string $view = 'filament.pages.risultati-medaglie';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'FASE DUE';

    protected static ?string $navigationLabel = 'Risultati Medaglie';

    protected static ?string $title = 'Risultati Medaglie';

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

        return 'Annata '.$annata->anno.': 5 punti alla prima scelta, 3 alla seconda, 1 alla terza. Solo visualizzazione, nulla viene salvato.';
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
        ];
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
            'risultato' => ($annata && $categoria) ? $this->elabora($categoria) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function elabora(Categoria $categoria): array
    {
        try {
            $risultato = (new Medaglie)->calcolaCategoria($categoria);
            $indicatori = (new Australian)->indicatori($risultato['candidati']);
        } catch (Throwable $e) {
            report($e);

            return ['errore' => $e->getMessage()];
        }

        return ['errore' => null, 'indicatori' => $indicatori] + $risultato;
    }
}
