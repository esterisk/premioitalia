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
            $candidati = $risultato['candidati'];
            $matrice = $risultato['matrice'];
            $duelli = $risultato['duelli'];
            $nome = fn (int $id): string => $indicatori[$id]['d'] ?? (string) $id;
            $sigla = fn (int $id): string => $indicatori[$id]['s'] ?? '?';
        @endphp

        <x-filament::section heading="Vincitore">
            @if ($risultato['vincitoriCondorcet'])
                @foreach ($risultato['vincitoriCondorcet'] as $id)
                    <div class="text-lg font-semibold text-gray-950 dark:text-white">{{ $nome($id) }}</div>
                @endforeach
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Vincitore Condorcet: batte ogni altro candidato nei confronti diretti
                    ({{ $risultato['schede'] }} schede valide).
                </p>
            @elseif ($risultato['ciclo'])
                <p class="text-sm text-danger-600 dark:text-danger-400">
                    Nessun vincitore Condorcet: nessun candidato batte tutti gli altri
                    (pareggio o ciclo di preferenze). La classifica sotto è ordinata per punti e,
                    a parità, per prime scelte.
                </p>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Nessun candidato.</p>
            @endif
        </x-filament::section>

        <x-filament::section heading="Classifica">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="py-2 pr-4">#</th>
                            <th class="py-2 pr-4">Candidato</th>
                            <th class="py-2 pr-4 text-right">Vittorie</th>
                            <th class="py-2 pr-4 text-right">Pareggi</th>
                            <th class="py-2 pr-4 text-right">Sconfitte</th>
                            <th class="py-2 pr-4 text-right">Punti</th>
                            <th class="py-2 text-right">1ª scelta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($risultato['classifica'] as $posizione => $riga)
                            <tr>
                                <td class="py-2 pr-4 tabular-nums">{{ $posizione + 1 }}</td>
                                <td class="py-2 pr-4 font-medium text-gray-950 dark:text-white">
                                    <span class="mr-1 inline-flex h-6 min-w-6 items-center justify-center rounded bg-gray-500 px-1.5 text-xs font-semibold text-white">{{ $sigla($riga['id']) }}</span>
                                    {{ $nome($riga['id']) }}
                                    @if (in_array($riga['id'], $risultato['vincitoriCondorcet'], true))
                                        <x-filament::badge color="success" size="sm" class="ml-1 inline-flex">vincitore</x-filament::badge>
                                    @endif
                                </td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $riga['vittorie'] }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $riga['pareggi'] }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $riga['sconfitte'] }}</td>
                                <td class="py-2 pr-4 text-right font-medium tabular-nums">{{ $riga['punti'] }}</td>
                                <td class="py-2 text-right tabular-nums">{{ $risultato['primeScelte'][$riga['id']] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section heading="Matrice dei confronti">
            <x-slot name="description">
                Ogni cella indica quante schede preferiscono il candidato di riga a quello di colonna.
                In verde il confronto vinto, in rosso quello perso.
            </x-slot>

            <div class="overflow-x-auto">
                <table class="text-sm">
                    <thead>
                        <tr>
                            <th class="p-2"></th>
                            @foreach ($candidati as $colonna)
                                <th class="p-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400" title="{{ $nome($colonna) }}">
                                    {{ $sigla($colonna) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidati as $riga)
                            <tr>
                                <th class="whitespace-nowrap p-2 pr-4 text-left text-xs font-medium text-gray-950 dark:text-white">
                                    <span class="mr-1 font-semibold text-gray-500 dark:text-gray-400">{{ $sigla($riga) }}</span>
                                    {{ $nome($riga) }}
                                </th>
                                @foreach ($candidati as $colonna)
                                    <td @class([
                                        'p-2 text-center tabular-nums',
                                        'bg-gray-100 text-gray-400 dark:bg-white/5' => $riga === $colonna,
                                        'bg-success-50 font-semibold text-success-700 dark:bg-success-500/10 dark:text-success-400' => $riga !== $colonna && $duelli[$riga][$colonna] > 0,
                                        'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' => $riga !== $colonna && $duelli[$riga][$colonna] < 0,
                                    ])>
                                        {{ $riga === $colonna ? '—' : $matrice[$riga][$colonna] }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
