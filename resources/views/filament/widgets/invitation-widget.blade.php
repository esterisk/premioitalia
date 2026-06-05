<x-filament-widgets::widget>
    <x-filament::section heading="Inviti">
        <div class="space-y-4">
            <select
                wire:model="conventionId"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white"
            >
                <option value="">Seleziona convention:</option>
                @foreach ($conventions as $id => $titolo)
                    <option value="{{ $id }}">{{ $titolo }}</option>
                @endforeach
            </select>

            <textarea
                wire:model="testo"
                rows="6"
                style="padding: 1em;margin-bottom: 1em;"
                placeholder="Inserisci i dati degli invitati..."
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-400"
            ></textarea>

            {{ $this->mandaInvitoAction }}
        </div>

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
