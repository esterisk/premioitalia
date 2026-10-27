<x-filament-widgets::widget>
    <x-filament::section heading="Albo d'oro">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Il Premio Italia {{ \App\Models\Annata::corrente()?->anno }} si è concluso: importa nell'albo
                i finalisti e i vincitori salvati per l'annata.
            </p>

            <div>
                {{ $this->importaAlboAction }}
            </div>
        </div>

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
