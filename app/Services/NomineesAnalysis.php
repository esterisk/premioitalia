<?php

namespace App\Services;

use App\Enums\StatoCandidatoEnum;
use App\Models\Annata;
use App\Models\Candidato;
use App\Models\Categoria;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Manager\SheetManager;
use OpenSpout\Writer\XLSX\Writer;

class NomineesAnalysis
{
    /** Voti sotto i quali un candidato valido non viene considerato significativo. */
    public const VOTI_MINIMI = 3;

    public const NOTA_VOTI_INSUFFICIENTI = 'Numero di voti insufficiente';

    public $annata;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->annata = Annata::corrente();
    }

    /**
     * Genera il file Excel con l'esito del voto di prima fase: un foglio per
     * ogni categoria attiva, con finalisti, candidati votati e scartati.
     *
     * @return string percorso assoluto del file generato
     */
    public function esportaVotiPrimaFase(): string
    {
        $percorso = storage_path('app/public/docs/PremioItaliaAnalisiPrimaFase-'.$this->annata->anno.'.xlsx');

        if (! is_dir(dirname($percorso))) {
            mkdir(dirname($percorso), 0755, true);
        }

        $writer = new Writer;
        $writer->openToFile($percorso);

        $nomiFogli = [];

        foreach (Categoria::attive()->values() as $indice => $categoria) {
            if ($indice > 0) {
                $writer->addNewSheetAndMakeItCurrent();
            }

            $writer->getCurrentSheet()->setName($this->nomeFoglio($categoria, $indice, $nomiFogli));

            foreach ($this->righeCategoria($categoria->nome, $this->candidatiCategoria($categoria)) as $riga) {
                $writer->addRow($riga);
            }
        }

        $writer->close();

        return $percorso;
    }

    /**
     * Compone il foglio di una categoria: titolo, poi un blocco per gruppo,
     * ciascuno preceduto da una riga vuota e dalla propria intestazione.
     *
     * @param  Collection<int, Candidato>  $candidati
     * @return array<int, Row>
     */
    public function righeCategoria(string $nomeCategoria, Collection $candidati): array
    {
        $grassetto = (new Style)->setFontBold();

        $righe = [Row::fromValues([$nomeCategoria.' - Risultati votazione prima fase'], $grassetto)];

        foreach ($this->gruppiCategoria($candidati) as $intestazione => $gruppo) {
            $righe[] = Row::fromValues([]);
            $righe[] = Row::fromValues([$intestazione], $grassetto);

            foreach ($gruppo as $valori) {
                $righe[] = Row::fromValues($valori);
            }
        }

        return $righe;
    }

    /**
     * Raggruppa i candidati nei tre blocchi del foglio, già ordinati: finalisti,
     * candidati validi con almeno VOTI_MINIMI voti, candidati poco votati ed esclusi.
     *
     * @param  Collection<int, Candidato>  $candidati
     * @return array<string, array<int, array<int, int|string>>>
     */
    public function gruppiCategoria(Collection $candidati): array
    {
        $valido = StatoCandidatoEnum::Valido->value;

        $finalisti = $candidati
            ->filter(fn ($candidato): bool => $candidato->stato === $valido && $candidato->finalista >= 1)
            ->sortBy('descrizione', SORT_NATURAL | SORT_FLAG_CASE)
            ->map(fn ($candidato): array => [$candidato->descrizione]);

        $nonFinalisti = $candidati
            ->filter(fn ($candidato): bool => $candidato->stato === $valido && $candidato->finalista < 1);

        $votati = $nonFinalisti
            ->filter(fn ($candidato): bool => $candidato->voti_fase1 >= self::VOTI_MINIMI)
            ->sortByDesc('voti_fase1')
            ->map(fn ($candidato): array => [$candidato->descrizione, $candidato->voti_fase1]);

        $pocoVotati = $nonFinalisti
            ->filter(fn ($candidato): bool => $candidato->voti_fase1 < self::VOTI_MINIMI)
            ->sortByDesc('voti_fase1')
            ->map(fn ($candidato): array => [$candidato->descrizione, $candidato->voti_fase1, self::NOTA_VOTI_INSUFFICIENTI]);

        $esclusi = $candidati
            ->filter(fn ($candidato): bool => $candidato->stato === StatoCandidatoEnum::Escluso->value)
            ->sortBy('descrizione', SORT_NATURAL | SORT_FLAG_CASE)
            ->map(fn ($candidato): array => [$candidato->descrizione, $candidato->voti_fase1, $candidato->motivo_esclusione ?: 'Escluso']);

        return [
            'FINALISTI' => $finalisti->values()->all(),
            'ALTRI CANDIDATI VALIDI' => $votati->values()->all(),
            'ESCLUSI' => $pocoVotati->concat($esclusi)->values()->all(),
        ];
    }

    /**
     * I candidati dell'annata corrente in una categoria, esclusi quelli spostati:
     * le loro segnalazioni sono già confluite su un altro candidato.
     *
     * @return Collection<int, Candidato>
     */
    private function candidatiCategoria(Categoria $categoria): Collection
    {
        return $categoria->candidati()
            ->where('anno', $this->annata->anno)
            ->where('stato', '!=', StatoCandidatoEnum::Spostato->value)
            ->orderBy('descrizione')
            ->get();
    }

    /**
     * Nome di foglio valido per Excel: senza caratteri proibiti, non più lungo
     * di 31 caratteri e diverso da quelli già assegnati.
     *
     * @param  array<int, string>  $nomiUsati
     */
    private function nomeFoglio(Categoria $categoria, int $indice, array &$nomiUsati): string
    {
        $nome = str_replace(['\\', '/', '?', '*', ':', '[', ']'], ' ', $categoria->nome_breve ?: $categoria->nome);
        $nome = trim(mb_substr(trim($nome), 0, SheetManager::MAX_LENGTH_SHEET_NAME));

        if ($nome === '') {
            $nome = 'Categoria '.($indice + 1);
        }

        $base = $nome;
        $suffisso = 1;
        while (in_array($nome, $nomiUsati, true)) {
            $suffisso++;
            $nome = trim(mb_substr($base, 0, SheetManager::MAX_LENGTH_SHEET_NAME - 3)).' '.$suffisso;
        }

        $nomiUsati[] = $nome;

        return $nome;
    }
}
