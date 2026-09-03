<?php

namespace App\Enums;

enum StatoApplicationEnum: string
{
    case InAttesa = 'PENDING';
    case DaVerificare = 'DA_VERIFICARE';

    public function label(): string
    {
        return match ($this) {
            self::InAttesa => 'In attesa di valutazione',
            self::DaVerificare => 'Da verificare',
        };
    }
}
