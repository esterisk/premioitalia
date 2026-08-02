<?php

namespace App\Calcolo;

/**
 * Trasforma l'elaborazione grezza dell'australian ballot (un array di turni,
 * ognuno con i voti per candidato) in una struttura pronta da disegnare, con
 * l'indicazione di chi viene eliminato ad ogni turno e di chi vince.
 */
class ElaborazioneAustralian
{
    /**
     * @param  array<int, array<int|string, int>>|string|null  $elaborazione  turni del conteggio, o il loro json
     * @param  array<int, int|string>  $vincitori  id dei candidati arrivati in fondo
     * @param  array<int|string, array{d: string, c: int, s: string}>  $indicatori  descrizioni e sigle per candidato
     * @return array<int, array{numero: int, totale: int, righe: array<int, array{id: int, sigla: string, descrizione: string, colore: int, voti: int, percentuale: float, larghezza: float, eliminato: bool, vincitore: bool}>}>
     */
    public static function turni(array|string|null $elaborazione, array $vincitori, array $indicatori): array
    {
        $turni = is_string($elaborazione) ? json_decode($elaborazione, true) : $elaborazione;

        if (! is_array($turni)) {
            return [];
        }

        $turni = array_values($turni);
        $vincitori = array_map('intval', $vincitori);
        $risultato = [];

        foreach ($turni as $indice => $turno) {
            $turno = (array) $turno;
            arsort($turno);

            $successivo = isset($turni[$indice + 1]) ? array_map('intval', array_keys((array) $turni[$indice + 1])) : null;
            $ultimo = $indice === count($turni) - 1;
            $totale = array_sum($turno);
            $massimo = $turno ? max($turno) : 0;
            $righe = [];

            foreach ($turno as $id => $voti) {
                $id = (int) $id;
                $righe[] = [
                    'id' => $id,
                    'sigla' => $indicatori[$id]['s'] ?? (string) $id,
                    'descrizione' => $indicatori[$id]['d'] ?? (string) $id,
                    'colore' => (int) ($indicatori[$id]['c'] ?? 0),
                    'voti' => (int) $voti,
                    'percentuale' => $totale > 0 ? round($voti / $totale * 100, 1) : 0.0,
                    'larghezza' => $massimo > 0 ? round($voti / $massimo * 100, 1) : 0.0,
                    'eliminato' => $successivo !== null && ! in_array($id, $successivo, true),
                    'vincitore' => $ultimo && in_array($id, $vincitori, true),
                ];
            }

            $risultato[] = [
                'numero' => $indice + 1,
                'totale' => $totale,
                'righe' => $righe,
            ];
        }

        return $risultato;
    }
}
