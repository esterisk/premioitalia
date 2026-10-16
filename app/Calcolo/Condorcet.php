<?php

namespace App\Calcolo;

use App\Models\Annata;
use App\Models\Candidato;
use App\Models\Categoria;

/**
 * Calcolo dei vincitori con il metodo Condorcet, solo in lettura.
 *
 * Ogni scheda ordina fino a tre candidati: i candidati indicati battono quelli
 * non indicati e, tra gli indicati, vince quello in posizione migliore.
 */
class Condorcet
{
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
     * @return array{
     *     candidati: array<int, int>,
     *     schede: int,
     *     matrice: array<int, array<int, int>>,
     *     duelli: array<int, array<int, int>>,
     *     classifica: array<int, array{id: int, vittorie: int, pareggi: int, sconfitte: int, punti: float}>,
     *     vincitoriCondorcet: array<int, int>,
     *     ciclo: bool,
     * }
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
        $matrice = self::matrice($candidati, $schede);

        $duelli = [];
        foreach ($candidati as $a) {
            foreach ($candidati as $b) {
                if ($a !== $b) {
                    $duelli[$a][$b] = $matrice[$a][$b] <=> $matrice[$b][$a];
                }
            }
        }

        $classifica = [];
        foreach ($candidati as $a) {
            $vittorie = count(array_filter($duelli[$a] ?? [], fn (int $esito): bool => $esito > 0));
            $sconfitte = count(array_filter($duelli[$a] ?? [], fn (int $esito): bool => $esito < 0));
            $pareggi = count($candidati) - 1 - $vittorie - $sconfitte;

            $classifica[] = [
                'id' => $a,
                'vittorie' => $vittorie,
                'pareggi' => $pareggi,
                'sconfitte' => $sconfitte,
                'punti' => $vittorie + $pareggi / 2,
            ];
        }

        $primeScelte = array_map(
            fn (int $id): int => count(array_filter($schede, fn (array $scheda): bool => $scheda[0] === $id)),
            array_combine($candidati, $candidati),
        );

        usort($classifica, fn (array $x, array $y): int => [$y['punti'], $primeScelte[$y['id']]] <=> [$x['punti'], $primeScelte[$x['id']]]);

        $massimo = count($candidati) - 1;
        $vincitoriCondorcet = array_values(array_map(
            fn (array $riga): int => $riga['id'],
            array_filter($classifica, fn (array $riga): bool => $massimo > 0 && $riga['vittorie'] === $massimo),
        ));

        return [
            'candidati' => $candidati,
            'schede' => count($schede),
            'matrice' => $matrice,
            'duelli' => $duelli,
            'classifica' => $classifica,
            'vincitoriCondorcet' => $vincitoriCondorcet,
            'ciclo' => $vincitoriCondorcet === [] && count($candidati) > 1,
            'primeScelte' => $primeScelte,
        ];
    }

    /**
     * Numero di schede in cui la riga è preferita alla colonna.
     *
     * @param  array<int, int>  $candidati
     * @param  array<int, array<int, int>>  $schede
     * @return array<int, array<int, int>>
     */
    public static function matrice(array $candidati, array $schede): array
    {
        $matrice = [];
        foreach ($candidati as $a) {
            foreach ($candidati as $b) {
                $matrice[$a][$b] = 0;
            }
        }

        foreach ($schede as $scheda) {
            $scheda = array_values(array_unique(array_filter($scheda, fn (int $id): bool => isset($matrice[$id]))));

            foreach ($scheda as $posizione => $preferito) {
                foreach ($candidati as $altro) {
                    if ($altro !== $preferito && ! in_array($altro, array_slice($scheda, 0, $posizione + 1), true)) {
                        $matrice[$preferito][$altro]++;
                    }
                }
            }
        }

        return $matrice;
    }
}
