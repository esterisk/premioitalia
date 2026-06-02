<?php

namespace App\Filament\Widgets;

use App\Models\Annata;
use Filament\Widgets\Widget;

class StatusWidget extends Widget
{
    protected string $view = 'filament.widgets.status-widget';

    protected int|string|array $columnSpan = 'full';

    public array $status = [];

    public function mount(): void
    {
        $annata = Annata::corrente();

        $this->status = $annata ? $annata->getStatus() : [];
    }
}
