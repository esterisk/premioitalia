@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">{{ $page_title }}</h1>
	</div>
	
	<div class="row">
		<div class="col-md-5 col-sm-5">
			<div class="card" style="margin: 0.5em 0 0.5em 0">
				<div class="card-body">
					<form class="form-inline">
						<label class="my-1 mr-2">Consulta per categoria</label>
						<select class="form-control form-control-sm my-1 mr-sm-2" onchange="location=this.options[this.selectedIndex].value">
							<option value="{{ route('albo') }}">tutte le categorie</option>
						@foreach (\App\Raggruppamento::all() as $r)
							<option value="{{ route('albo-raggruppamento', [ 'raggruppamento' => $r->slug ]) }}"{{ $r->slug == $raggruppamento ? ' selected' : ''}}>{{ $r->raggruppamento }}</option>
						@endforeach
						</select>
					</form>
				</div>
			</div>		
		</div>

		<div class="col-md-5 col-sm-5">
			<div class="card" style="margin: 0.5em 0 0.5em 0">
				<div class="card-body">
					<form class="form-inline">
						<label class="my-1 mr-2">Consulta per anno</label>
						<select class="form-control form-control-sm my-1 mr-sm-2" onchange="location=this.options[this.selectedIndex].value">
							<option value="{{ route('albo') }}">tutti gli anni</option>
						@foreach (\App\Albo::distinct('anno')->orderBy('anno','desc')->pluck('anno') as $a)
							<option value="{{ route('albo-anno', [ 'anno' => $a ]) }}"{{ $a == $anno ? ' selected' : ''}}>{{ $a }}</option>
						@endforeach
						</select>
					</form>
				</div>
			</div>		
		</div>
	</div>

	<div class="row">
		<div class="col-md-10 col-sm-10">
			<div class="card" style="margin: 0.5em 0 0.5em 0">
				<div class="card-body">
					<form class="form-inline" onsubmit="return false">
					<label for="filter" class="my-1 mr-2">Filtro</label>
					<input type="text" class="form-control" id="filter" placeholder="Nome o titolo" onkeyup="alboSearch(this.value)">
					<span class="my-1 ml-2" id="filterFound"></span>
				</div>
			</div>		
		</div>
	</div>

	<div class="albo">
	<?php $anno = false; ?>
	@foreach ($albo as $winner)
		@if ($anno != $winner->anno)
		<?php $categoria = false; ?>
		@if ($anno)
			</div>
		</div>		
		@endif
		<div class="row albo-anno">
			<div class="col-sm-1">
				<h2><a href="{{ route('albo-anno', [ 'anno' => $winner->anno]) }}" title="Vedi dettagli sul Premio Italia {{ $anno = $winner->anno }}">{{ $anno = $winner->anno }}</a></h2>
			</div>
			<div class="col-sm-11">
				<?php $con = \App\Convention::whereAnno($winner['anno'])->where('italcon','>',0)->first(); ?>
				@if ($con)
				<h2>{{ $con->titolo_edizione ? $con->titolo_edizione.' - ' : '' }}Italcon {{ $con->italcon }}</h2>
				<p class="convention">{{ $con->city }}{{ $con->premiazione ? ', '.Cutter::date($con->premiazione, 'd M Y') : '' }}</p>
				@else
				<h2>(Nessuna italcon)</h2>
				<p class="convention">Premi assegnati l'anno successivo</p>
				@endif
		@endif
				<div class="winner" data-desc="{!! str_replace('"','',strtolower($winner->descrizione)) !!}">
					@if ($categoria != $categorie[$winner->categoria_id])
					<h4>{{ $categoria = $categorie[$winner->categoria_id] }}</h4>
					@endif
					<p class="winner {{ $winner->finalista == 1 ? 'finalista' : ''}}">
					{!! $winner->descrizione_ricca() !!}
					@if ($posizione && $winner->categoria->supercategoria != 'Premi speciali')<span class="posizione">({{ $winner->finalista == 2 ? 'vincitore' : ($winner->posizione > 0 ? $winner->posizione.'° classificato' : 'finalista') }})</span> @endif
					</p>
				</div>
	@endforeach
			</div>
		</div>
	</div>

@endsection
