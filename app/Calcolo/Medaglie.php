<?php

namespace App\Calcolo;

use App\Models\Annata;
use App\Models\Candidato;
use App\Models\Categoria;

/**
 * Calcolo dei vincitori a punti, solo in lettura: ogni prima scelta vale
 * 5 punti, ogni seconda 3 e ogni terza 1.
 */
class Medaglie
{
    /**
     * Punti assegnati per posizione di preferenza.
     *
     * @var array<int, int>
     */
    public const PUNTI = [1 => 5, 2 => 3, 3 => 1];

    public ?Annata $annata = null;

    /**
     * Carica l'annata da elaborare, per anno o corrente.
     */
    public function caricaAnnata(int|false $anno = false): ?Annata
    {
        $this->annata = $anno ? Annata::whereAnno($anno)->first() : Annata::corrente();

        return $this->annata;
    }

    /**
     * Elabora una categoria senza salvare nulla.
     *
     * @return array<string, mixed>
     */
    public function calcolaCategoria(Categoria|int $categoria): array
    {
        if (! $this->annata) {
            $this->caricaAnnata();
        }

        $categoriaId = $categoria instanceof Categoria ? $categoria->getKey() : $categoria;

        $candidati = Candidato::whereAnno($this->annata->anno)
            ->whereCategoriaId($categoriaId)
            ->where('finalista', '>', 0)
            ->orderBy('descrizione')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $candidati[] = -1;

        return self::elabora($candidati, $this->caricaSchede($categoriaId));
    }

    /**
     * Schede della categoria come elenchi ordinati di candidati.
     *
     * @return array<int, array<int, int>>
     */
    public function caricaSchede(int $categoriaId): array
    {
        $schede = [];

        $preferenze = $this->annata->preferenze()
            ->wherePreferenzaCategoriaId($categoriaId)
            ->where('candidato_1_id', '<>', 0)
            ->get();

        foreach ($preferenze as $preferenza) {
            $scheda = [(int) $preferenza->candidato_1_id];

            // "Nessun premio" (-1) non ammette ulteriori scelte.
            if ($preferenza->candidato_1_id > 0) {
                foreach ([$preferenza->candidato_2_id, $preferenza->candidato_3_id] as $scelta) {
                    if ($scelta) {
                        $scheda[] = (int) $scelta;
                    }
                }
            }

            $schede[] = $scheda;
        }

        return $schede;
    }

    /**
     * @param  array<int, int>  $candidati
     * @param  array<int, array<int, int>>  $schede
     * @return array<string, mixed>
     */
    public static function elabora(array $candidati, array $schede): array
    {
        $conti = [];
        foreach ($candidati as $id) {
            $conti[$id] = ['id' => $id, 1 => 0, 2 => 0, 3 => 0, 'punti' => 0];
        }

        foreach ($schede as $scheda) {
            $scheda = array_values(array_unique($scheda));

            foreach (array_slice($scheda, 0, count(self::PUNTI)) as $indice => $id) {
                if (isset($conti[$id])) {
                    $conti[$id][$indice + 1]++;
                    $conti[$id]['punti'] += self::PUNTI[$indice + 1];
                }
            }
        }

        $classifica = array_values($conti);
        usort($classifica, fn (array $x, array $y): int => [$y['punti'], $y[1], $y[2], $y[3]] <=> [$x['punti'], $x[1], $x[2], $x[3]]);

        $vincitori = [];
        $migliore = $classifica[0] ?? null;
        foreach ($classifica as $riga) {
            if ($migliore && $riga['punti'] === $migliore['punti'] && $riga['punti'] > 0 && $riga[1] === $migliore[1] && $riga[2] === $migliore[2] && $riga[3] === $migliore[3]) {
                $vincitori[] = $riga['id'];
            }
        }

        return [
            'candidati' => $candidati,
            'schede' => count($schede),
            'classifica' => $classifica,
            'vincitori' => $vincitori,
            'parita' => count($vincitori) > 1,
        ];
    }
}
