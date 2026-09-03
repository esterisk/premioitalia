<?php

use App\Services\ExifPosition;
use Carbon\Carbon;

/**
 * Dati EXIF come li restituisce exif_read_data(): coordinate in gradi, minuti e
 * secondi espressi come frazioni, più il riferimento dell'emisfero.
 *
 * @return array<string, mixed>
 */
function exifCastello(array $sovrascrivi = []): array
{
    return array_merge([
        'GPSLatitude' => ['45/1', '28/1', '1287/100'],
        'GPSLatitudeRef' => 'N',
        'GPSLongitude' => ['9/1', '10/1', '4662/100'],
        'GPSLongitudeRef' => 'E',
        'DateTimeOriginal' => '2026:09:03 12:00:00',
    ], $sovrascrivi);
}

beforeEach(fn () => Carbon::setTestNow('2026-09-03 12:30:00'));

afterEach(fn () => Carbon::setTestNow());

it('converte le coordinate EXIF in gradi decimali', function () {
    $posizione = ExifPosition::fromExifData(exifCastello());

    expect($posizione->hasCoordinate())->toBeTrue()
        ->and($posizione->latitudine)->toBeGreaterThan(45.4702)->toBeLessThan(45.4703)
        ->and($posizione->longitudine)->toBeGreaterThan(9.1796)->toBeLessThan(9.1797);
});

it('rende negative le coordinate a sud e a ovest', function () {
    $posizione = ExifPosition::fromExifData(exifCastello([
        'GPSLatitudeRef' => 'S',
        'GPSLongitudeRef' => 'W',
    ]));

    expect($posizione->latitudine)->toBeLessThan(0)
        ->and($posizione->longitudine)->toBeLessThan(0);
});

it('accetta coordinate già in forma numerica', function () {
    $posizione = ExifPosition::fromExifData([
        'GPSLatitude' => [45, 28, 12.87],
        'GPSLatitudeRef' => 'N',
        'GPSLongitude' => [9, 10, 46.62],
        'GPSLongitudeRef' => 'E',
    ]);

    expect($posizione->latitudine)->toBeGreaterThan(45.4702)->toBeLessThan(45.4703);
});

it('non ricava coordinate se i dati GPS mancano o sono incompleti', function () {
    expect(ExifPosition::fromExifData([])->hasCoordinate())->toBeFalse()
        ->and(ExifPosition::fromExifData(['GPSLatitude' => ['45/1'], 'GPSLatitudeRef' => 'N'])->hasCoordinate())->toBeFalse()
        ->and(ExifPosition::fromExifData(exifCastello(['GPSLongitude' => null]))->hasCoordinate())->toBeFalse();
});

it('legge la data di scatto dai campi EXIF', function () {
    expect(ExifPosition::fromExifData(exifCastello())->scattataIl->toDateTimeString())->toBe('2026-09-03 12:00:00');

    $digitalizzata = ExifPosition::fromExifData([
        'DateTimeDigitized' => '2026:09:03 11:00:00',
    ]);
    expect($digitalizzata->scattataIl->toDateTimeString())->toBe('2026-09-03 11:00:00');
});

it('considera recente uno scatto di meno di un ora fa', function () {
    expect(ExifPosition::fromExifData(exifCastello())->recente())->toBeTrue();
});

it('non considera recente uno scatto di più di un ora fa', function () {
    $vecchia = ExifPosition::fromExifData(exifCastello(['DateTimeOriginal' => '2026:09:03 10:59:00']));

    expect($vecchia->recente())->toBeFalse();
});

it('supera la verifica sull età se la data di scatto non è disponibile', function () {
    $senzaData = ExifPosition::fromExifData(exifCastello(['DateTimeOriginal' => null]));

    expect($senzaData->scattataIl)->toBeNull()
        ->and($senzaData->recente())->toBeTrue();
});

it('ignora una data di scatto illeggibile', function () {
    $illeggibile = ExifPosition::fromExifData(exifCastello(['DateTimeOriginal' => '0000:00:00 00:00:00']));

    expect($illeggibile->recente())->toBeTrue();
});

it('restituisce una posizione vuota se il file non esiste o non ha EXIF', function () {
    expect(ExifPosition::fromFile(null)->hasCoordinate())->toBeFalse()
        ->and(ExifPosition::fromFile('/percorso/inesistente.jpg')->hasCoordinate())->toBeFalse()
        ->and(ExifPosition::fromFile(__FILE__)->hasCoordinate())->toBeFalse();
});
