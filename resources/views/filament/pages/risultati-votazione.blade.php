@php
    use App\Filament\Pages\RisultatiVotazione;

    $classeColore = function (int $id, int $indice): string {
        if ($id < 0) {
            return 'bg-gray-400';
        }

        return RisultatiVotazione::COLORI[max(0, $indice - 1) % count(RisultatiVotazione::COLORI)];
    };
@endphp

<x-filament-panels::page>
    <x-filament::section>
        <div class="flex flex-wrap items-end gap-4">
            <div class="grow max-w-md">
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

            @if ($risultato)
                <div class="flex flex-wrap items-center gap-2">
                    @if (! ($risultato['salvato'] ?? null))
                        <x-filament::badge color="gray" icon="heroicon-o-clock">Mai salvata</x-filament::badge>
                    @elseif ($risultato['allineato'] ?? false)
                        <x-filament::badge color="success" icon="heroicon-o-check-circle">
                            Salvata il {{ $risultato['salvato']->updated_at?->format('d/m/Y H:i') }}
                        </x-filament::badge>
                    @else
                        <x-filament::badge color="warning" icon="heroicon-o-exclamation-triangle">
                            Il risultato salvato è diverso dal calcolo attuale
                        </x-filament::badge>
                    @endif
                </div>
            @endif
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
            $conteggio = $risultato['conteggio'];
            $indicatori = $risultato['indicatori'];
            $conti = $risultato['conti'];
        @endphp

        <x-filament::section heading="Podio">
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ([['Primo classificato', $risultato['vincitori'], 'text-amber-600 dark:text-amber-400'], ['Secondo classificato', $risultato['secondi'], 'text-gray-500 dark:text-gray-400'], ['Terzo classificato', $risultato['terzi'], 'text-orange-700 dark:text-orange-400']] as [$posizione, $assegnati, $classe])
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
                        <div class="text-xs font-medium uppercase tracking-wide {{ $classe }}">{{ $posizione }}</div>
                        @forelse ($assegnati as $id)
                            <div class="mt-1 font-medium text-gray-950 dark:text-white">
                                {{ $indicatori[$id]['d'] ?? $id }}
                            </div>
                        @empty
                            <div class="mt-1 text-sm text-gray-400">non assegnato</div>
                        @endforelse
                        @if (count($assegnati) > 1)
                            <div class="mt-2 text-xs text-danger-600 dark:text-danger-400">parità non risolta</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section heading="Partecipazione">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
                @foreach ([
                    'Votanti categoria' => $conteggio->votanti,
                    'Votanti seconda fase' => $conteggio->totale_votanti,
                    '% votanti' => $conteggio->percentuale_votanti.'%',
                    'Preferenze espresse' => $conteggio->preferenze,
                    'Prime scelte vincitore' => $conteggio->preferenze_vincitore,
                ] as $etichetta => $valore)
                    <div class="rounded-lg bg-gray-50 p-3 dark:bg-white/5">
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $etichetta }}</div>
                        <div class="text-xl font-semibold tabular-nums text-gray-950 dark:text-white">{{ $valore }}</div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section heading="Finalisti">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="py-2 pr-4">Sigla</th>
                            <th class="py-2 pr-4">Candidato</th>
                            <th class="py-2 pr-4 text-right">1ª scelta</th>
                            <th class="py-2 pr-4 text-right">2ª scelta</th>
                            <th class="py-2 pr-4 text-right">3ª scelta</th>
                            <th class="py-2 text-right">Totale</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($risultato['finalisti'] as $id)
                            <tr>
                                <td class="py-2 pr-4">
                                    <span class="inline-flex h-6 min-w-6 items-center justify-center rounded px-1.5 text-xs font-semibold text-white {{ $classeColore($id, $indicatori[$id]['c'] ?? 0) }}">
                                        {{ $indicatori[$id]['s'] ?? '?' }}
                                    </span>
                                </td>
                                <td class="py-2 pr-4 font-medium text-gray-950 dark:text-white">
                                    {{ $indicatori[$id]['d'] ?? $id }}
                                    @if (in_array($id, $risultato['vincitori'], true))
                                        <x-filament::badge color="success" size="sm" class="ml-1 inline-flex">vincitore</x-filament::badge>
                                    @endif
                                </td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $conti[$id][1] ?? 0 }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $id > 0 ? ($conti[$id][2] ?? 0) : '—' }}</td>
                                <td class="py-2 pr-4 text-right tabular-nums">{{ $id > 0 ? ($conti[$id][3] ?? 0) : '—' }}</td>
                                <td class="py-2 text-right font-medium tabular-nums">{{ $conti[$id][0] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        @foreach ($risultato['fasi'] as $fase)
            <x-filament::section :heading="$fase['titolo']">
                <x-slot name="description">
                    {{ count($fase['turni']) }} {{ count($fase['turni']) === 1 ? 'turno' : 'turni' }} di conteggio.
                    Ad ogni turno il candidato con meno prime scelte viene eliminato e le sue preferenze
                    vengono ridistribuite sulla scelta successiva.
                </x-slot>

                <div class="overflow-x-auto">
                    <div class="flex min-w-max items-stretch gap-3">
                        @foreach ($fase['turni'] as $turno)
                            <div class="w-64 shrink-0 rounded-lg border border-gray-200 p-3 dark:border-white/10">
                                <div class="mb-3 flex items-baseline justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Turno {{ $turno['numero'] }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $turno['totale'] }} voti</span>
                                </div>

                                <div class="space-y-2">
                                    @foreach ($turno['righe'] as $riga)
                                        <div @class([
                                            'rounded-md px-2 py-1.5',
                                            'bg-danger-50 dark:bg-danger-500/10' => $riga['eliminato'],
                                            'bg-success-50 dark:bg-success-500/10' => $riga['vincitore'],
                                        ])>
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="flex items-center gap-1.5 overflow-hidden">
                                                    <span class="inline-flex h-5 min-w-5 shrink-0 items-center justify-center rounded px-1 text-[10px] font-semibold text-white {{ $classeColore($riga['id'], $riga['colore']) }}">
                                                        {{ $riga['sigla'] }}
                                                    </span>
                                                    <span @class([
                                                        'truncate text-xs',
                                                        'text-gray-600 dark:text-gray-300' => ! $riga['eliminato'],
                                                        'text-danger-600 line-through dark:text-danger-400' => $riga['eliminato'],
                                                    ]) title="{{ $riga['descrizione'] }}">
                                                        {{ $riga['descrizione'] }}
                                                    </span>
                                                </span>
                                                <span class="shrink-0 text-xs font-semibold tabular-nums text-gray-950 dark:text-white">
                                                    {{ $riga['voti'] }}
                                                </span>
                                            </div>

                                            <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                                <div class="h-full rounded-full {{ $riga['eliminato'] ? 'bg-danger-500' : $classeColore($riga['id'], $riga['colore']) }}"
                                                     style="width: {{ $riga['larghezza'] }}%"></div>
                                            </div>

                                            <div class="mt-1 flex items-center justify-between text-[10px]">
                                                <span class="text-gray-400">{{ $riga['percentuale'] }}%</span>
                                                @if ($riga['eliminato'])
                                                    <span class="font-medium text-danger-600 dark:text-danger-400">eliminato</span>
                                                @elseif ($riga['vincitore'])
                                                    <span class="font-medium text-success-600 dark:text-success-400">assegnato</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    @endif
</x-filament-panels::page>
