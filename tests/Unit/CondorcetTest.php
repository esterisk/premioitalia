<?php

use App\Calcolo\Condorcet;

test('la matrice conta le schede in cui un candidato è preferito all\'altro', function () {
    $matrice = Condorcet::matrice([1, 2, 3], [[1, 2], [2], [1, 3, 2]]);

    expect($matrice[1][2])->toBe(2)
        ->and($matrice[2][1])->toBe(1)
        ->and($matrice[1][3])->toBe(1)
        ->and($matrice[3][1])->toBe(0)
        ->and($matrice[2][3])->toBe(1)
        ->and($matrice[3][2])->toBe(1)
        ->and($matrice[1][1])->toBe(0);
});

test('individua il vincitore Condorcet', function () {
    $risultato = Condorcet::elabora([1, 2, 3], [[1, 2], [1, 3], [2, 1], [3, 1, 2]]);

    expect($risultato['vincitoriCondorcet'])->toBe([1])
        ->and($risultato['ciclo'])->toBeFalse()
        ->and($risultato['classifica'][0]['id'])->toBe(1)
        ->and($risultato['classifica'][0]['vittorie'])->toBe(2);
});

test('segnala il ciclo quando nessuno batte tutti', function () {
    $risultato = Condorcet::elabora([1, 2, 3], [[1, 2, 3], [2, 3, 1], [3, 1, 2]]);

    expect($risultato['vincitoriCondorcet'])->toBe([])
        ->and($risultato['ciclo'])->toBeTrue();
});

test('ignora candidati sconosciuti e duplicati nelle schede', function () {
    $matrice = Condorcet::matrice([1, 2], [[99, 1, 1, 2]]);

    expect($matrice[1][2])->toBe(1)->and($matrice[2][1])->toBe(0);
});
