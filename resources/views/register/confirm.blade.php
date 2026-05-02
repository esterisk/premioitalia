@extends('layouts.app')

@section('content')

<div class="row">
	<h1 class="col-sm-12">Ammissione nuovi elettori</h1>
</div>

<div class="row">
	<div class="col-12">
		@if($error == 'empty')
		<p class="mt-5 alert alert-danger">
			La richiesta non è valida. Apri la mail ricevuta dal comitato organizzatore e fai clic sul pulsante "Premio Italia ammissione elettori". Se i problemi persistono scrivi a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.
		</p>
		@endif

		@if($error == 'expired')
		<p class="mt-5  alert alert-danger">
			La richiesta è scaduta. Per favore richiedi nuovamente l'iscrizione.
		</p>
		@endif

		@if($error == 'invalid')
		<p class="mt-5  alert alert-danger">
			Il codice non sembra corretto. Apri la mail ricevuta dal comitato organizzatore e fai clic sul pulsante "Premio Italia ammissione elettori". Se i problemi persistono scrivi a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.
		</p>
		@endif

		@if($error == 'registered')
		<div class="mt-5  alert alert-success">
			<h4 class="alert-heading">Eri già iscritto!</h4>
			Risulti già registrato tra i votanti: a posto così! Presto riceverai la mail di invito al voto.
		</div>
		@endif

		@if(!$error)
		<div class="mt-5">
			<h3 class="alert-heading">Conferma l'iscrizione</h3>
			<p>Grazie per aver richiesto l'iscrizione agli elettori del Premio Italia.</p>
			<p>Per favore verifica che i dati qui sotto siano corretti. Se lo sono premi il pulsante “Conferma l'iscrizione”.</p>
			<p>Se c'è un errore per favore scrivi a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>. Se è un errore minimo se vuoi puoi confermare l'iscrizione comunque e poi chiedere via email la correzione.</p>

			<dl class="dati">
				<dt>Nome</dt>
				<dd>{{ $invitation->firstname }}</dd>
				<dt>Cognome</dt>
				<dd>{{ $invitation->lastname }}</dd>
				<dt>Email</dt>
				<dd>{{ $invitation->email }}</dd>
			</dl>

			<form method="post" action="{{route('iscrizione.conferma')}}">
				@csrf
				<input type="hidden" name="token" value="{{ $invitation->token }}">
				<input type="hidden" name="email" value="{{ $invitation->email }}">
				<button class="btn btn-primary" type="submit">Conferma l'iscrizione</button>
			</form>
		</div>

		@endif

	</div>
</div>

@endsection