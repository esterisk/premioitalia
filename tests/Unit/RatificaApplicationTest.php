<?php

use App\Enums\StatoApplicationEnum;
use App\Mail\BenvenutoElettore;
use App\Models\Application;
use App\Models\User;

/**
 * Lo schema legacy (annate, users...) non è nelle migrazioni: i test non usano il database.
 */
it('permette di ratificare solo le richieste non ancora ratificate', function () {
    $application = (new Application)->setRawAttributes(['status' => 'PENDING']);
    expect($application->isRatificabile())->toBeTrue();

    $application->setRawAttributes(['status' => 'RATIFICATA']);
    expect($application->status)->toBe(StatoApplicationEnum::Ratificata)
        ->and($application->isRatificabile())->toBeFalse();
});

it('rende la mail di benvenuto', function () {
    $user = (new User)->setRawAttributes(['firstname' => 'Mario', 'email' => 'mario@example.com']);

    $mail = new BenvenutoElettore($user);

    expect($mail->build()->subject)->toBe('Benvenuto fra gli elettori del Premio Italia');
});
