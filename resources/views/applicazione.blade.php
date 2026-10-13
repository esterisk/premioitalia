@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Richiesta di iscrizione al voto</h1>
	</div>

	@if(!$convention)

		<div class="row">
			<div class="col-sm-12">
				<div class="alert alert-warning">
					Non risulta nessuna convention in corso in questo momento. Questa pagina permette di iscriversi al voto del Premio Italia solo durante lo svolgimento di una convention affiliata.
				</div>
			</div>
		</div>

	@else

		<div class="row">
			<p class="col-sm-12">
				Sei a <strong>{{ $convention->titolo_edizione }}</strong>? Puoi chiedere qui di essere iscritto fra gli elettori del Premio Italia. Consenti al browser di rilevare la tua posizione: serve solo a verificare che ti trovi effettivamente alla convention. La richiesta sarà valutata al più presto e riceverai un'email di conferma.
			</p>
		</div>

		<livewire:applicazione-form :convention="$convention" />

	@endif
@endsection
