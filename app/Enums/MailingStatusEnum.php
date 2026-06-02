<?php

namespace App\Enums;

enum MailingStatusEnum: string
{
    case Idle = 'idle';
    case Waiting = 'access-waiting';
    case Sending = 'access-sending';
    case Checking = 'access-checking';
    case Problem = 'problem';

    public function label()
    {
        return match ($this) {
            self::Waiting => 'Pronta',
            self::Idle => 'In attesa',
            self::Sending => 'In esecuzione',
            self::Checking => 'Risposta arrivata',
            self::Problem => 'Errore',
        };
    }
}
