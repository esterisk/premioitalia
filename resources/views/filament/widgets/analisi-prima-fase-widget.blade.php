<x-filament-widgets::widget>
    <x-filament::section heading="Analisi voto prima fase">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Genera il file Excel con l'esito del voto di prima fase dell'annata
                {{ \App\Models\Annata::corrente()?->anno }}: un foglio per categoria attiva, con finalisti,
                altri candidati validi ed esclusi. Il file viene salvato in <code>storage/app/public/docs</code>.
            </p>

            <div class="flex items-center gap-3">
                {{ $this->generaAnalisiAction }}
            </div>
        </div>

        @if($urlFile)
            <div class="mt-4 text-sm">
                <span class="text-gray-500 dark:text-gray-400">Ultimo file generato:</span>
                <a href="{{ $urlFile }}" class="font-medium text-primary-600 underline dark:text-primary-400">
                    {{ $nomeFile }}
                </a>
            </div>
        @endif

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
