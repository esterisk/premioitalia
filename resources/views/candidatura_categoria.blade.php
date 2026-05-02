@extends('layouts.app')

@section('content')
<section class="candidature">
	<div class="row">
		<div class="col-sm-12">
			<h1>Candidature spontanee: {{ $categoria->nome }}</h1>
			<h1 class="h2">Premio Italia {{ $annata->anno }}</h1>
		</div>
	</div>

	@if ($annata->candidatureAperte())
	<div class="row" id="aggiungi-no">
		<p class="col-sm-12"><a class="btn btn-primary" href="#aggiungi"><i class="fa fa-plus"></i> Aggiungi nuova</a></p>
	</div>
	<div class="row" id="aggiungi" style="display:none">
		<p class="col-sm-12 text-right"><a class="btn btn-light" href="#" id="candidatura-form-close"><i class="fa fa-times"></i> Chiudi modulo</a></p>
		<div class="col-sm-12">
		
			<div class="alert alert-primary" role="alert">
				<strong>Istruzioni</strong> Verifica con attenzione le condizioni richieste (data di uscita, lunghezza, eccetera). Inserisci tutti i campi. Una volta inviata la candidatura essa <strong>non comparirà nell'elenco</strong> finché non sarà stata validata dalla redazione.
			</div>
			
			<p class="definizione">{!! str_replace('periodo', ($annata->anno - 1), $categoria->definizione) !!} 
			@if($categoria->esclusi_ultimi > 0) <p>Non sono candidabili i vincitori delle ultime {{ $categoria->esclusi_ultimi }} edizioni:
			</p>
				<ul>
				@foreach ($categoria->ultimiVincitori($annata->anno) as $candidato)
					<li>{{ $candidato->descrizione }} ({{ $candidato->anno }})</li>
				@endforeach
				</ul>	 
			@endif

			<div class="detail">
				<dl>
				@foreach($categoria->regole as $regola)
					<dt>{{ $regola->titolo }}</dt>
					<dd>{{ $regola->testo }}</dd>
				@endforeach
				</dl>
			</div>
			<div class="segnalazioni">
				<form method="post" id="candidatura-form" action="{{ url()->current() }}">
				{{ csrf_field() }}
				<table class="table">
				<tbody>
					<tr>
						@foreach ($categoria->campi as $c)
						<th>{{ Config::get('premioitalia.campi.'.$c) }}</th>
						@endforeach
					</tr>
					<tr>
						@foreach ($categoria->campi as $c)
						<td><input type="text" class="form-control" value="" title="{{ Config::get('premioitalia.campi.'.$c) }}" placeholder="{{ Config::get('premioitalia.campi.'.$c) }}" name="{{ $c }}"></td>
						@endforeach
					</tr>
					<tr>
						<td colspan="{{ count($categoria->campi) }}" class="text-right">
							<div class="alert alert-success candidatura-alert" style="display:none">Grazie, segnalazione inserita! Sarà verificata e inserita in lista entro 48 ore.</div>
							<div class="alert alert-danger candidatura-alert" style="display:none"></div>
							<button class="btn btn-primary">Invia</button>
						</td>
					</tr>
				</tbody>
				</table>
				</form>
			</div>

		</div>
	</div>
	@else
	<div class="row" id="aggiungi-no">
		<p class="col-sm-12">Il termine per l'invio di candidature spontanee è scaduto</p>
	</div>
	@endif
	
	<div class="row">
		<h2 class="col-sm-12">Segnalazioni pervenute</h2>
	</div>
	<div class="row">
		<div class="col-sm-12">
		<ul>
			@forelse ($categoria->candidature()->valide()->inRandomOrder()->get() as $c)

				<li>{!! $c->descrizione_ricca() !!}</li>
				
			@empty
			
				</ul><p>Nessuna segnalazione finora.</p><ul>
			
			@endforelse
		</ul>
		</div>
	</div>

	<div class="row back">
		<p class="col-sm-12"><a href="{{ route('candidature') }}"><i class="fa fa-chevron-left"></i>Altre categorie</a></p>
	</div>
	
</section>
@endsection
