<?php

use App\Calcolo\Nominees;

/**
 * Nominees si costruisce leggendo l'annata corrente dal database: per testare
 * la sola logica di calcolo lo istanziamo saltando il costruttore.
 */
function nominees(bool $dryRun = true): Nominees
{
    $nominees = (new ReflectionClass(Nominees::class))->newInstanceWithoutConstructor();
    $nominees->dryRun = $dryRun;
    $nominees->anno = 2026;

    return $nominees;
}

function conteggio(): object
{
    return new class
    {
        public int $candidati = 0;

        public int $candidati_validi = 0;

        public int $segnalazioni_valide = 0;

        public int $categoria_valida = 10;

        public int $percentuale_votanti = 100;

        public float $media = 0;

        public int $voti_minimi = 0;
    };
}

/**
 * @param  array<int, int>  $segnalazioni
 * @return array<int, object>
 */
function candidati(array $segnalazioni): array
{
    return array_map(fn (int $count): object => (object) ['segnalazioni_count' => $count], $segnalazioni);
}

it('scarta massimo e minimo nel calcolo della media', function () {
    $conteggio = conteggio();

    // 10 e 1 vengono scartati: media su 8, 5, 3 => 16 / 3
    nominees()->massimiMinimi($conteggio, candidati([10, 8, 5, 3, 1]));

    expect($conteggio->media)->toBe(16 / 3)
        ->and($conteggio->voti_minimi)->toBe(7)
        ->and($conteggio->candidati)->toBe(5)
        ->and($conteggio->candidati_validi)->toBe(4)
        ->and($conteggio->segnalazioni_valide)->toBe(26);
});

it('non conta i candidati senza segnalazioni', function () {
    $conteggio = conteggio();

    nominees()->massimiMinimi($conteggio, candidati([6, 4, 2, 0, 0]));

    expect($conteggio->candidati)->toBe(3)
        ->and($conteggio->candidati_validi)->toBe(2);
});

it('non divide per zero quando nessun candidato ha segnalazioni', function () {
    $conteggio = conteggio();

    nominees()->massimiMinimi($conteggio, candidati([0, 0]));

    expect($conteggio->media)->toBe(0.0)
        ->and($conteggio->voti_minimi)->toBe(1);
});

it('seleziona i candidati che raggiungono i voti minimi', function () {
    $conteggio = conteggio();
    $conteggio->voti_minimi = 5;
    $conteggio->candidati_validi = 6;

    $finalisti = nominees()->selezionaFinalisti(null, candidati([12, 9, 7, 6, 5, 4, 2]), $conteggio);

    expect($finalisti)->toHaveCount(5)
        ->and(array_column($finalisti, 'segnalazioni_count'))->toBe([12, 9, 7, 6, 5]);
});

it('ammette piu di cinque finalisti a parita di segnalazioni', function () {
    $conteggio = conteggio();
    $conteggio->voti_minimi = 5;
    $conteggio->candidati_validi = 7;

    $finalisti = nominees()->selezionaFinalisti(null, candidati([12, 9, 7, 6, 6, 6, 4]), $conteggio);

    expect($finalisti)->toHaveCount(6)
        ->and(array_column($finalisti, 'segnalazioni_count'))->toBe([12, 9, 7, 6, 6, 6]);
});

it('invalida la categoria con un solo candidato valido e pochi votanti', function () {
    $conteggio = conteggio();
    $conteggio->voti_minimi = 3;
    $conteggio->candidati_validi = 1;
    $conteggio->percentuale_votanti = 25;

    nominees()->selezionaFinalisti(null, candidati([8]), $conteggio);

    expect($conteggio->categoria_valida)->toBe(0);
});

it('declassa la categoria sotto il dieci per cento di votanti', function () {
    $conteggio = conteggio();
    $conteggio->voti_minimi = 3;
    $conteggio->candidati_validi = 4;
    $conteggio->percentuale_votanti = 8;

    nominees()->selezionaFinalisti(null, candidati([8, 6, 5, 4]), $conteggio);

    expect($conteggio->categoria_valida)->toBe(5);
});
