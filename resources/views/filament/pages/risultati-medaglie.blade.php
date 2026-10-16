<x-filament-panels::page>
    <x-filament::section>
        <div class="max-w-md">
            <label for="categoria" class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">
                Categoria
            </label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="categoriaId" id="categoria">
                    @foreach ($categorie as $unaCategoria)
                        <option value="{{ $unaCategoria->getKey() }}">{{ $unaCategoria->nome }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </x-filament::section>

    @if (! $annata)
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">Nessuna annata configurata.</p>
        </x-filament::section>
    @elseif (! $categoria)
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">Nessuna categoria attiva da elaborare.</p>
        </x-filament::section>
    @elseif ($risultato['errore'] ?? null)
        <x-filament::section heading="Errore nel conteggio">
            <p class="text-sm text-danger-600 dark:text-danger-400">{{ $risultato['errore'] }}</p>
        </x-filament::section>
    @else
        @php
            $indicatori = $risultato['indicatori'];
            $nome = fn (int $id): string => $indicatori[$id]['d'] ?? (string) $id;
            $sigla = fn (int $id): string => $indicatori[$id]['s'] ?? '?';
            $massimo = max(1, collect($risultato['classifica'])->max('punti'));
        @endphp

        <x-filament::section heading="Podio">
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ([['Primo classificato', 0, 'text-amber-600 dark:text-amber-400'], ['Secondo classificato', 1, 'text-gray-500 dark:text-gray-400'], ['Terzo classificato', 2, 'text-orange-700 dark:text-orange-400']] as [$titolo, $indice, $classe])
                    @php $riga = $risultato['classifica'][$indice] ?? null; @endphp
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
                        <div class="text-xs font-medium uppercase tracking-wide {{ $classe }}">{{ $titolo }}</div>
                        @if ($riga && $riga['punti'] > 0)
                            <div class="mt-1 font-medium text-gray-950 dark:text-white">{{ $nome($riga['id']) }}</div>
                            <div class="text-sm tabular-nums text-gray-500 dark:text-gray-400">{{ $riga['punti'] }} punti</div>
                        @else
                            <div class="mt-1 text-sm text-gray-400">non assegnato</div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if ($risultato['parita'])
                <p class="mt-3 text-sm text-danger-600 dark:text-danger-400">
                    Parità al primo posto anche dopo il confronto tra prime, seconde e terze scelte.
                </p>
            @endif
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                {{ $risultato['schede'] }} schede. A parità di punti decidono prime, seconde e terze scelte.
            </p>
        </x-filament::section>

        <x-filament::section heading="Classifica">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="py-2 pr-4">#</th>
                            <th class="py-2 pr-4">Candidato</th>
                            <th class="py-2 pr-4 text-right">1ª (×5)</th>
                            <th class="py-2 pr-4 text-right">2ª (×3)</th>
                            <th class="py-2 pr-4 text-right">3ª (×1)</th>
                            <th class="py-2 pr-4 text-right">Punti</th>
                            <th class="w-40 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($risultato['classifica'] as $posizione => $riga)
                            <tr>
                                <td class="py-2 pr-4 tabular-nums">{{ $posizione + 1 }}</td>
                                <td class="py-2 pr-4 font-medium text-gray-950 dark:text-white">
                                    <span class="mr-1 inline-flex h-6 min-w-6 items-center justify-center rounded bg-gray-500 px-1.5 text-xs font-semibold text-white">{{ $sigla($riga['id']) }}</span>
                                    {{ $nome($riga['id']) }}
                                    @if (in_array($riga['id'], $risultato['vincitori'], true))
                                        <x-filament::badge color="success" size="sm" class="ml-1 inline-flex">vincitore</x-filament::badge>
                                    @endif
                                </td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $riga[1] }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $riga[2] }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $riga[3] }}</td>
                                <td class="py-2 pr-4 text-right font-semibold tabular-nums text-gray-950 dark:text-white">{{ $riga['punti'] }}</td>
                                <td class="py-2">
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                        <div class="h-full rounded-full bg-amber-500" style="width: {{ round($riga['punti'] / $massimo * 100) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
