<x-filament-widgets::widget>
    <x-filament::section heading="Stato corrente">
        @if(empty($status))
            <p class="text-sm text-gray-500">Nessuna annata corrente.</p>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-lg bg-gray-50 dark:bg-white/5 px-4 py-3">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Annata</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $status['year'] }}</p>
                </div>

                <div class="rounded-lg bg-gray-50 dark:bg-white/5 px-4 py-3">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Fase</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ str_replace('fase','fase ',$status['phase']) }}</p>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $status['faseRimasti'] }}/{{ $status['faseGiorniTotali']}} giorni rimanenti</p>
                </div>

                <div class="rounded-lg bg-gray-50 dark:bg-white/5 px-4 py-3">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Stato mailing</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $status['mailing_status'] }}</p>
                </div>

                @if($status['phase'] !== 'pre')
                    <div class="rounded-lg bg-gray-50 dark:bg-white/5 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Votanti</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $status['voters'] }}</p>
                    </div>
                @endif

            </div>

            @if($status['phase'] !== 'pre')
                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">

                    <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Voti fase 1</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['votes1'] }}</p>
                    </div>

                    @if($status['phase'] !== 'fase1')
                        <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Voti fase 2</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['votes2'] }}</p>
                        </div>
                    @endif

                    <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">In preparazione</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['drafts'] }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Inviti spediti</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['invitations'] }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Promemoria spediti</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['reminders'] }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Solleciti spediti</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['solicitations'] }}</p>
                    </div>

                </div>
            @else
                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">

                    <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Inviti spediti</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['invitations'] }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Promemoria spediti</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['reminders'] }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-white/10 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Solleciti spediti</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $status['solicitations'] }}</p>
                    </div>

                </div>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
