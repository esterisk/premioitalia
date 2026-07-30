<x-filament-widgets::widget>
    <x-filament::section heading="Selezione finalisti">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Calcola i conteggi delle segnalazioni e seleziona i finalisti per ogni categoria attiva
                dell'annata {{ \App\Models\Annata::corrente()?->anno }}.
            </p>

            <div class="flex items-center gap-3">
                {{ $this->simulaSelezioneAction }}
                {{ $this->calcolaFinalistiAction }}
            </div>
        </div>

        @if($risultati)
            <div class="mt-4">
                <x-filament::badge :color="$dryRun ? 'warning' : 'success'">
                    {{ $dryRun ? 'Simulazione (nessun dato salvato)' : 'Selezione salvata' }}
                </x-filament::badge>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="py-2 pr-4">Categoria</th>
                            <th class="py-2 pr-4 text-right">Media</th>
                            <th class="py-2 pr-4 text-right">Voti minimi</th>
                            <th class="py-2 pr-4 text-right">Cand. validi</th>
                            <th class="py-2 pr-4 text-right">% votanti</th>
                            <th class="py-2">Finalisti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($risultati as $risultato)
                            <tr class="align-top">
                                <td class="py-2 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ $risultato['categoria'] }}
                                    @if($risultato['categoria_valida'] < 10)
                                        <x-filament::badge color="danger" size="sm" class="mt-1">
                                            validità {{ $risultato['categoria_valida'] }}
                                        </x-filament::badge>
                                    @endif
                                </td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $risultato['media'] }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $risultato['voti_minimi'] }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $risultato['candidati_validi'] }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $risultato['percentuale_votanti'] }}%</td>
                                <td class="py-2 text-gray-700 dark:text-gray-300">
                                    @forelse($risultato['finalisti'] as $finalista)
                                        <div>{{ $finalista }}</div>
                                    @empty
                                        <span class="text-gray-400">nessuno</span>
                                    @endforelse
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
