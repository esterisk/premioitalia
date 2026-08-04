<?php

namespace App\Filament\Widgets;

use App\Models\Annata;
use App\Services\NomineesAnalysis;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class AnalisiPrimaFaseWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected string $view = 'filament.widgets.analisi-prima-fase-widget';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    /** Nome del file generato nell'ultima esecuzione. */
    public ?string $nomeFile = null;

    /** Url pubblico del file generato nell'ultima esecuzione. */
    public ?string $urlFile = null;

    public static function canView(): bool
    {
        return in_array(Annata::corrente()?->fase(), ['fase1', 'spoglio1', 'fase2', 'spoglio2', 'post'], true);
    }

    public function generaAnalisiAction(): Action
    {
        return Action::make('generaAnalisi')
            ->label('Genera e scarica')
            ->icon('heroicon-o-table-cells')
            ->color('primary')
            ->action(fn (): ?BinaryFileResponse => $this->generaAnalisi());
    }

    protected function generaAnalisi(): ?BinaryFileResponse
    {
        if (! Annata::corrente()) {
            Notification::make()->title('Nessuna annata corrente')->danger()->send();

            return null;
        }

        try {
            $percorso = (new NomineesAnalysis)->esportaVotiPrimaFase();
        } catch (Throwable $e) {
            report($e);

            Notification::make()
                ->title('Errore durante la generazione del file')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }

        $this->nomeFile = basename($percorso);
        $this->urlFile = Storage::disk('public')->url('docs/'.$this->nomeFile);

        Notification::make()
            ->title('File generato: '.$this->nomeFile)
            ->success()
            ->send();

        return response()->download($percorso);
    }
}
