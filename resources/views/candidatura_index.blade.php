@extends('layouts.app')

@section('content')
<section class="candidature">
	<div class="row">
		<div class="col-sm-12">
			<h1>Candidature spontanee</h1>
			<h1 class="h2">Premio Italia {{ $annata->anno }}</h1>
			<p>In questa pagina è possibile segnalare proattivamente candidature per l'edizione dell'anno corrente del Premio Italia. La candidatura è anonima. Le candidature vengono valutate dalla redazione e, se considerate valide rispetto al regolamento del premio e della specifica categoria,
				aggiunte alla lista delle candidature spontanee.</p>
			<p>All'apertura della prima fase di voto del Premio Italia non sarà più possibile inserire candidature spontanee, ma i votanti potranno consultare la lista e votare, se lo desiderano, una della candidature proposte.</p>
		</div>
	</div>
	<div class="row">
		<h2 class="col-sm-12">Categorie</h2>
	</div>
	<div class="row">
		<div class="col-sm-12">
			@foreach (\App\Models\Categoria::attive() as $categoria)
			<p><a href="{{ route('candidature-categoria', $categoria->slug) }}">{{ $categoria->nome }}</a>
				@if (($q = $categoria->candidature()->valide()->count()) > 0)
				({{ $q > 1 ? $q.' candidature' : 'una candidatura' }})
				@else
				(-)
				@endif
				@if ($annata->candidatureAperte())
				<small><a href="{{ route('candidature-categoria', $categoria->slug) }}#aggiungi">( <i class="fa fa-plus"></i> aggiungi nuova )</a></small>
			</p>
			@endif
			@endforeach


		</div>
	</div>
	<div class="row">
		<h2 class="col-sm-12">Ultime segnalazioni pervenute</h2>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<ul>
				@forelse (\App\Models\Candidatura::valide()->orderBy('created_at','desc')->limit(5)->get() as $c)

				<li><strong>{{ $c->categoria->nome }}:</strong> {!! $c->descrizione_ricca() !!}</li>

				@empty

			</ul>
			<p>Nessuna segnalazione finora.</p>
			<ul>

				@endforelse
			</ul>
		</div>
	</div>

	@if ($scarti = \App\Models\Candidatura::nonValide()->orderBy('created_at','desc')->get())
	<div class="row">
		<h2 class="col-sm-12">Ultime segnalazioni scartate</h2>
		<p class="col-sm-12" style="font-size:80%; margin-bottom: 1em">(Se non siete d'accordo o volete spiegazioni scrivete a staff@premioitalia.org)</p>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<ul>
				@foreach ($scarti as $c)

				<li><strong>{{ $c->categoria->nome }}:</strong> {!! $c->descrizione_ricca() !!}: <i>{{ $c->motivo_esclusione }}</i></li>

				@endforeach
			</ul>
		</div>
	</div>
	@endif

</section>
@endsection