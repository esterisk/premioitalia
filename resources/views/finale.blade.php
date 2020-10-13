@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">{{ $page_title }}</h1>
	</div>
	@if (!$annata->finalisti())
	<div class="row">
		<div class="col-sm-12">
			<p>I vincitori {{ $annata->anno }} non sono ancora calcolabili.</p>
		</div>
	</div>
	@else
	
	<p>Votazione aperta dal {{ Cutter::date($annata->fase_2_da, 'd M') }} al {{ Cutter::date($annata->fase_2_a, 'd M Y') }}.</p>
	<p>Premiazione la notte del {{ Cutter::date($annata->premiazione, 'd M Y') }} durante {{ $annata->convention->titolo_edizione }} a {{ $annata->convention->city }}.</p>
	<div class="finalisti finale">
	@foreach ($categorie as $categoria)
	@php $c = $conteggio[$categoria->id]; @endphp
		<div class="row">
			<div class="col-sm-12">
				<h2>{{ $categoria->nome }}</h2>
				<dl>
					<dt>Votanti seconda fase</dt> <dd>{{ $c->votanti }}, {{ $c->percentuale_votanti }}% su {{ $c->totale_votanti }}</dd>
					<dt>Preferenze</dt> <dd>{{ $c->preferenze }}</dd>
					<dt>Finalisti</dt> <dd>{{ $c->finalisti - 1 }}</dd>
				</dl>
				<ul>
					<li><i>Primo classificato</i> 
					@foreach ( explode(',',$c->vincitori) as $v) 
						{{ $v == -1 ? 'Nessun premio' : $c->indicatori[$v]['d'] }}
					@endforeach
					</li>
					@if ($c->secondi)
					<li><i>Secondo classificato</i> 
					@foreach ( explode(',',$c->secondi) as $v) 
						{{ $v == -1 ? 'Nessun premio' : $c->indicatori[$v]['d'] }}
					@endforeach
					</li>
					@endif
					@if ($c->terzi)
					<li><i>Terzo classificato</i> 
					@foreach ( explode(',',$c->terzi) as $v) 
						{{ $v == -1 ? 'Nessun premio' : $c->indicatori[$v]['d'] }}
					@endforeach
					</li>
					@endif
				</ul>
				<h5>Finalisti</h5>
				<table class="table table-sm table-hover">
					<thead>
						<tr>
							<th class="text-center">•</th>
							<th>Candidato</th>
							<th colspan="3">Voti</th>
						</tr>
						
					</thead>
					<tbody>
				@foreach ($c->elenco_finalisti as $cid)
					<tr>
						<td class="text-center"><span class="badge badge-f f{{ $c->indicatori[$cid]['c'] }}">{{ $c->indicatori[$cid]['s'] }}</span></td>
						<td>{{ $c->indicatori[$cid]['d'] }}</td>
						<td class="conti-item"><span class="badge badge-c c1">1</span> {{ $c->conti_candidati[$cid][1] }}</td>
						<td class="conti-item">
							@if($cid > 0)
							<span class="badge badge-c c2">2</span> {{ $c->conti_candidati[$cid][2] }}
							@endif
						</td>
						<td class="conti-item">
							@if($cid > 0)
								<span class="badge badge-c c3">3</span> {{ $c->conti_candidati[$cid][3] }}</span>
							@endif
						</td>
					</tr>
				@endforeach
				</table>

				<h5>Elaborazione vincitore</h5>
				<table class="elab"><tr>
				@foreach (json_decode($c->elaborazione) as $step)
					<td>
					@foreach ($step as $cid => $voti)
						<div><span class="badge f{{ $c->indicatori[$cid]['c'] }}">{{ $c->indicatori[$cid]['s'] }}</span> <span class="voti">{{ $voti }}</span></div>
					@endforeach
					</td>
				@endforeach
				</tr></table>

				@if (!empty($c->secondi))
				<h5>Elaborazione secondo posto</h5>
				<table class="elab"><tr>
				@foreach (json_decode($c->elaborazione_2) as $step)
					<td>
					@foreach ($step as $cid => $voti)
						<div><span class="badge f{{ $c->indicatori[$cid]['c'] }}">{{ $c->indicatori[$cid]['s'] }}</span> <span class="voti">{{ $voti }}</span></div>
					@endforeach
					</td>
				@endforeach
				</tr></table>
				@endif

				@if (!empty($c->terzi))
				<h5>Elaborazione terzo posto</h5>
				<table class="elab"><tr>
				@foreach (json_decode($c->elaborazione_3) as $step)
					<td>
					@foreach ($step as $cid => $voti)
						<div><span class="badge f{{ $c->indicatori[$cid]['c'] }}">{{ $c->indicatori[$cid]['s'] }}</span> <span class="voti">{{ $voti }}</span></div>
					@endforeach
					</td>
				@endforeach
				</tr></table>
				@endif
	
			</div>
		</div>
	@endforeach
	
	@endif
	
@endsection
