<?php

use App\Models\Convention;

/**
 * Coordinate del Castello Sforzesco e del Duomo di Milano: circa 1,6 km di distanza.
 */
const CASTELLO_LAT = 45.470249;
const CASTELLO_LNG = 9.179617;
const DUOMO_LAT = 45.464211;
const DUOMO_LNG = 9.191383;

/**
 * Il test è unitario e non avvia l'applicazione: gli attributi vanno impostati
 * grezzi, come arriverebbero dal database, perché il cast in scrittura richiede
 * una connessione.
 */
function convention(?array $location = ['lat' => CASTELLO_LAT, 'lng' => CASTELLO_LNG]): Convention
{
    return (new Convention)->setRawAttributes(['location' => $location === null ? null : json_encode($location)]);
}

it('calcola la distanza fra due coordinate in metri', function () {
    $distanza = Convention::distanzaFraCoordinate(CASTELLO_LAT, CASTELLO_LNG, DUOMO_LAT, DUOMO_LNG);

    expect($distanza)->toBeGreaterThan(1000)->toBeLessThan(1200);
});

it('considera nulla la distanza fra un punto e se stesso', function () {
    expect(Convention::distanzaFraCoordinate(CASTELLO_LAT, CASTELLO_LNG, CASTELLO_LAT, CASTELLO_LNG))->toBe(0.0);
});

it('conferma le coordinate entro mezzo chilometro dalla location', function () {
    // circa 200 metri a nord della location
    expect(convention()->coordinateVicine(CASTELLO_LAT + 0.0018, CASTELLO_LNG))->toBeTrue();
});

it('non conferma le coordinate oltre mezzo chilometro dalla location', function () {
    expect(convention()->coordinateVicine(DUOMO_LAT, DUOMO_LNG))->toBeFalse();
});

it('non conferma la posizione se le coordinate mancano', function () {
    expect(convention()->coordinateVicine(null, null))->toBeFalse()
        ->and(convention()->coordinateVicine(CASTELLO_LAT, null))->toBeFalse();
});

it('non conferma la posizione se la convention non ha una location', function () {
    expect(convention(null)->hasLocation())->toBeFalse()
        ->and(convention(null)->coordinateVicine(CASTELLO_LAT, CASTELLO_LNG))->toBeFalse()
        ->and(convention(['lat' => CASTELLO_LAT])->coordinateVicine(CASTELLO_LAT, CASTELLO_LNG))->toBeFalse();
});

it('espone la distanza dalla location solo quando è calcolabile', function () {
    expect(convention()->distanzaDa(DUOMO_LAT, DUOMO_LNG))->toBeGreaterThan(1000)
        ->and(convention()->distanzaDa(null, null))->toBeNull()
        ->and(convention(null)->distanzaDa(DUOMO_LAT, DUOMO_LNG))->toBeNull();
});

it('usa mezzo chilometro come raggio di conferma', function () {
    expect(Convention::RAGGIO_APPLICAZIONE)->toBe(500);
});
