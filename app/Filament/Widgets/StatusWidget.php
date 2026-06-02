<?php

namespace App\Filament\Widgets;

use App\Models\Annata;
use Filament\Widgets\Widget;

class StatusWidget extends Widget
{
    protected string $view = 'filament.widgets.status-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $annata = Annata::corrente();

        return [
            'status' => $annata ? $annata->getStatus() : [],
        ];
    }
}
