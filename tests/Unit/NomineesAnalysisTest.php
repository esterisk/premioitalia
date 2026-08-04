<?php

use App\Services\NomineesAnalysis;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Row;

/**
 * NomineesAnalysis legge l'annata corrente dal database nel costruttore: per
 * testare la sola composizione delle righe lo istanziamo saltandolo.
 */
function analisi(): NomineesAnalysis
{
    return (new ReflectionClass(NomineesAnalysis::class))->newInstanceWithoutConstructor();
}

function candidato(string $descrizione, int $voti, int $finalista = 0, string $stato = 'valido', ?string $motivo = null): object
{
    return (object) [
        'descrizione' => $descrizione,
        'voti_fase1' => $voti,
        'finalista' => $finalista,
        'stato' => $stato,
        'motivo_esclusione' => $motivo,
    ];
}

/**
 * I tre gruppi appiattiti in un unico elenco, per verificarne il contenuto e
 * l'ordine senza ripetere le intestazioni in ogni aspettativa.
 *
 * @param  Collection<int, object>  $candidati
 * @return array<int, array<int, int|string>>
 */
function righe(Collection $candidati): array
{
    return array_merge(...array_values(analisi()->gruppiCategoria($candidati)));
}

it('elenca prima i finalisti in ordine alfabetico e senza voti', function () {
    $righe = righe(collect([
        candidato('Zeta', 20, finalista: 1),
        candidato('alfa', 5, finalista: 2),
        candidato('Beta', 30, finalista: 1),
    ]));

    expect($righe)->toBe([['alfa'], ['Beta'], ['Zeta']]);
});

it('elenca i non finalisti con almeno tre voti in ordine decrescente', function () {
    $righe = righe(collect([
        candidato('Poco votato', 4),
        candidato('Il più votato', 12),
        candidato('Finalista', 99, finalista: 1),
        candidato('Nel mezzo', 7),
    ]));

    expect($righe)->toBe([
        ['Finalista'],
        ['Il più votato', 12],
        ['Nel mezzo', 7],
        ['Poco votato', 4],
    ]);
});

it('segnala in terza colonna i voti insufficienti e i motivi di esclusione', function () {
    $righe = righe(collect([
        candidato('Escluso', 8, stato: 'escluso', motivo: 'Fuori anno'),
        candidato('Due voti', 2),
        candidato('Un voto', 1),
        candidato('Ammesso', 3),
    ]));

    expect($righe)->toBe([
        ['Ammesso', 3],
        ['Due voti', 2, NomineesAnalysis::NOTA_VOTI_INSUFFICIENTI],
        ['Un voto', 1, NomineesAnalysis::NOTA_VOTI_INSUFFICIENTI],
        ['Escluso', 8, 'Fuori anno'],
    ]);
});

it('non tratta come finalisti i candidati non validi', function () {
    $righe = righe(collect([
        candidato('Escluso ma finalista', 10, finalista: 1, stato: 'escluso', motivo: 'Ritirato'),
    ]));

    expect($righe)->toBe([['Escluso ma finalista', 10, 'Ritirato']]);
});

it('ripiega su una nota generica se manca il motivo di esclusione', function () {
    $righe = righe(collect([
        candidato('Senza motivo', 6, stato: 'escluso'),
    ]));

    expect($righe)->toBe([['Senza motivo', 6, 'Escluso']]);
});

it('non elenca nessun candidato per una categoria vuota', function () {
    expect(righe(new Collection))->toBe([]);
});

it('intitola il foglio e separa i gruppi con una riga vuota e una intestazione', function () {
    $righe = analisi()->righeCategoria('Romanzo di Fantascienza', collect([
        candidato('Finalista', 40, finalista: 1),
        candidato('Votato', 9),
        candidato('Escluso', 4, stato: 'escluso', motivo: 'Fuori anno'),
    ]));

    expect(array_map(fn (Row $riga): array => $riga->toArray(), $righe))->toBe([
        ['Romanzo di Fantascienza - Risultati votazione prima fase'],
        [],
        ['FINALISTI'],
        ['Finalista'],
        [],
        ['ALTRI CANDIDATI VALIDI'],
        ['Votato', 9],
        [],
        ['ESCLUSI'],
        ['Escluso', 4, 'Fuori anno'],
    ]);
});

it('mette in grassetto il titolo e le intestazioni di gruppo', function () {
    $righe = analisi()->righeCategoria('Fumetto', collect([
        candidato('Votato', 9),
    ]));

    $grassetto = array_map(fn (Row $riga): bool => $riga->getStyle()->isFontBold(), $righe);

    expect($grassetto)->toBe([true, false, true, false, true, false, false, true]);
});
