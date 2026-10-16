<?php

use App\Calcolo\Medaglie;

test('assegna 5, 3 e 1 punti per prima, seconda e terza scelta', function () {
    $risultato = Medaglie::elabora([1, 2, 3], [[1, 2, 3], [1, 3], [2, 1]]);

    $punti = collect($risultato['classifica'])->pluck('punti', 'id')->all();

    expect($punti)->toBe([1 => 13, 2 => 8, 3 => 4])
        ->and($risultato['vincitori'])->toBe([1])
        ->and($risultato['classifica'][0][1])->toBe(2);
});

test('a parità di punti decidono le prime scelte', function () {
    $risultato = Medaglie::elabora([1, 2], [[1, 2], [2, 1]]);

    expect($risultato['parita'])->toBeTrue()
        ->and($risultato['vincitori'])->toHaveCount(2);

    // 1 e 2 chiudono a 5 punti: 1 con una prima scelta, 2 con una seconda e due terze.
    $risultato = Medaglie::elabora([1, 2, 3, 4], [[1], [3, 2], [4, 3, 2], [4, 3, 2], [3, 4]]);
    $punti = collect($risultato['classifica'])->pluck('punti', 'id')->all();

    expect($punti[1])->toBe(5)->and($punti[2])->toBe(5);
    expect(array_search(1, array_column($risultato['classifica'], 'id')))
        ->toBeLessThan(array_search(2, array_column($risultato['classifica'], 'id')));
});

test('ignora candidati sconosciuti e duplicati', function () {
    $risultato = Medaglie::elabora([1, 2], [[99, 1, 1]]);

    expect(collect($risultato['classifica'])->pluck('punti', 'id')->all())->toBe([1 => 3, 2 => 0]);
});
