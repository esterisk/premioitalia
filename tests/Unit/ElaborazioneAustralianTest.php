<?php

use App\Calcolo\ElaborazioneAustralian;

/**
 * @param  array<int, int>  $candidati
 * @return array<int, array{d: string, c: int, s: string}>
 */
function indicatori(array $candidati): array
{
    $indicatori = [];
    foreach ($candidati as $i => $id) {
        $indicatori[$id] = $id === -1
            ? ['d' => 'Nessun premio', 'c' => 0, 's' => 'np']
            : ['d' => 'Candidato '.$id, 'c' => $i + 1, 's' => 'C'.$id];
    }

    return $indicatori;
}

it('segna eliminato chi non compare nel turno successivo', function () {
    $turni = ElaborazioneAustralian::turni(
        [[10 => 5, 20 => 3, 30 => 2], [10 => 6, 20 => 4]],
        [],
        indicatori([10, 20, 30]),
    );

    expect($turni)->toHaveCount(2)
        ->and(array_column($turni[0]['righe'], 'eliminato'))->toBe([false, false, true])
        ->and(array_column($turni[1]['righe'], 'eliminato'))->toBe([false, false]);
});

it('segna vincitore solo nell ultimo turno', function () {
    $turni = ElaborazioneAustralian::turni(
        [[10 => 5, 20 => 3], [10 => 7]],
        [10],
        indicatori([10, 20]),
    );

    expect(array_column($turni[0]['righe'], 'vincitore'))->toBe([false, false])
        ->and(array_column($turni[1]['righe'], 'vincitore'))->toBe([true]);
});

it('ordina le righe per voti decrescenti e calcola totale, percentuale e larghezza', function () {
    $turni = ElaborazioneAustralian::turni([[10 => 2, 20 => 6, 30 => 2]], [], indicatori([10, 20, 30]));

    expect($turni[0]['totale'])->toBe(10)
        ->and(array_column($turni[0]['righe'], 'id'))->toBe([20, 10, 30])
        ->and(array_column($turni[0]['righe'], 'percentuale'))->toBe([60.0, 20.0, 20.0])
        ->and(array_column($turni[0]['righe'], 'larghezza'))->toBe([100.0, 33.3, 33.3]);
});

it('legge l elaborazione anche come json e riporta sigle e descrizioni', function () {
    $turni = ElaborazioneAustralian::turni(json_encode([[10 => 4, -1 => 1]]), [10], indicatori([10, -1]));

    expect($turni[0]['righe'][0])
        ->toMatchArray(['id' => 10, 'sigla' => 'C10', 'descrizione' => 'Candidato 10', 'voti' => 4])
        ->and($turni[0]['righe'][1])
        ->toMatchArray(['id' => -1, 'sigla' => 'np', 'descrizione' => 'Nessun premio', 'voti' => 1]);
});

it('non si rompe su elaborazioni vuote', function () {
    expect(ElaborazioneAustralian::turni(null, [], []))->toBe([])
        ->and(ElaborazioneAustralian::turni('', [], []))->toBe([])
        ->and(ElaborazioneAustralian::turni('[]', [], []))->toBe([])
        ->and(ElaborazioneAustralian::turni([], [], []))->toBe([]);
});
