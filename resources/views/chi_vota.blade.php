@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">{{ $page_title }}</h1>
	</div>
	<div class="row row-regolamento">
		<div class="col-sm-12">
			<p>Possono votare al Premio Italia, nella prima fase di segnalazione delle opere e nella seconda di preferenza tra i finalisti, tutti coloro che hanno partecipato almeno a una edizione dell'Italcon, il convegno italiano degli appassionati di fantascienza, istituito nel 1972 (vedi l'<a href="{{ route('italcon') }}">elenco completo delle edizioni</a>), oppure a una edizione delle convention affiliate:</p>
			<ul>
			@foreach (\App\ConventionSeries::where('affiliata_da','>',0)->orderBy('nome')->get() as $con)
				<li>{{ $con->nome }} (dal {{ $con->affiliata_da }})</li>
			@endforeach
			</ul>
			<p>Per farsi accreditare è necessario scrivere a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>, chiedendo l'iscrizione al voto e allegando un qualche tipo di prova (per esempio, la foto del badge di partecipazione alla convention).</p>
			<p>Una volta iscritti si riceverà l'invito al voto ogni anno senza dover fare altro.</p>
			<p>È possibile disiscriversi cliccando il pulsante "Rimuovimi dai votanti dal Premio Italia - Unsubscribe" sulla mail che si riceve per l'accesso al voto o scrivendo a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
		</div>	
	</div>
	
@endsection
