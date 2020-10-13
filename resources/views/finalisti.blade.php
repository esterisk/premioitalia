@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">{{ $page_title }}</h1>
	</div>
	@if (!$annata->finalisti())
	<div class="row">
		<div class="col-sm-12">
			<p>I finalisti {{ $annata->anno }} non sono ancora disponibili.</p>
		</div>
	</div>
	@else
	
	<p>Votazione aperta dal {{ Cutter::date($annata->fase_2_da, 'd M') }} al {{ Cutter::date($annata->fase_2_a, 'd M Y') }}.</p>
	<p>Premiazione la notte del {{ Cutter::date($annata->premiazione, 'd M Y') }} durante {{ $annata->convention->titolo_edizione }} a {{ $annata->convention->city }}.</p>
	<div class="finalisti">
	@foreach ($categorie as $categoria)
		<div class="row">
			<div class="col-sm-12">
				<h2>{{ $categoria->nome }}</h2>
				<dl>
					<dt>Votanti prima fase</dt> <dd>{{ $categoria->conteggio->votanti }}, {{ $categoria->conteggio->percentuale_votanti }}% su {{ $categoria->conteggio->totale_votanti }}</dd>
					<dt>Voti</dt> <dd>{{ $categoria->conteggio->segnalazioni }} ({{ $categoria->conteggio->segnalazioni_valide }} validi)</dd>
					<dt>Candidati</dt> <dd>{{ $categoria->conteggio->candidati }} ({{ $categoria->conteggio->candidati_validi }} validi e con più di due segnalazioni)</dd>
					<dt>Media troncata</dt> <dd>{{ number_format($categoria->conteggio->media,2,',','') }}</dd>
					<dt>SMF*</dt> <dd>{{ $categoria->conteggio->voti_minimi }}</dd>
				@if ($annata->risultati())
					<dt><br/>Votanti seconda fase</dt> <dd>{{ $categoria->conteggioFinale->votanti }}, {{ $categoria->conteggioFinale->percentuale_votanti }}% su {{ $categoria->conteggioFinale->totale_votanti }}</dd>
					<dt>Finalisti</dt> <dd>{{ $categoria->conteggioFinale->finalisti }}</dd>
				@endif
				</dl>
				
				@if ($annata->risultati())
				<p>Vincitore</p>
				<ul>
				@foreach ($categoria->vincitori($annata->anno)->get() as $vincitore)
					<li><strong>{!! $vincitore->descrizione_ricca() !!}</strong></li>
				@endforeach
				</ul>
				@endif
			
				<p>Finalisti (in ordine alfabetico)</p>
				<ul>
				@foreach ($categoria->finalisti($annata->anno, $annata->risultati())->get() as $finalista)
					<li>{!! $finalista->descrizione_ricca() !!}</li>
				@endforeach
				</ul>
			</div>
		</div>
	@endforeach
	<dl><dt>* SMF</dt> <dd>soglia minima da raggiungere per essere ammissibili tra i finalisti; se non viene raggiunta tale quota, anche rientrando tra i primi cinque classificati non si può essere ammessi alla finale. Raggiungere o superare questa quota non è comunque sufficiente a essere ammessi in finale, risultato per il quale è necessario classificarsi nei primi cinque.</dd></dl>
	</div>
	

	@if (file_exists(public_path('docs/PremioItaliaAnalisi-'.$annata->anno.'.xlsx')))
	<div class="row row-download">
		<p><a href="docs/PremioItaliaAnalisi-{{ $annata->anno }}.xlsx"><img src="/images/xlsx.png"> Scarica l'analisi del voto (prima fase) con l'elenco dei candidati non finalisti e non validi</a></p>
	</div>
	@endif

	
	@endif
	
@endsection
