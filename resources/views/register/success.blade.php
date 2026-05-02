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
		<p class="mt-5 alert alert-danger">
			La richiesta è scaduta. Per favore richiedi nuovamente l'iscrizione.
		</p>
		@endif

		@if($error == 'invalid')
		<p class="mt-5 alert alert-danger">
			Il codice non sembra corretto. Apri la mail ricevuta dal comitato organizzatore e fai clic sul pulsante "Premio Italia ammissione elettori". Se i problemi persistono scrivi a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.
		</p>
		@endif

		@if($error == 'registered')
		<div class="mt-5 alert alert-success">
			<h4 class="alert-heading">Eri già registrato!</h4>
			Risulti già registrato tra i votanti: a posto così! Presto riceverai la mail di invito al voto.
		</div>
		@endif

		@if(!$error)
		<div class="mt-5 alert alert-success">
			<h4 class="alert-heading">Complimenti, ora sei un Elettore registrato!</h4>
			<p>Grazie per esserti registrato al voto al Premio Italia.</p>
			<p>Per accedere al voto non è necessaria alcuna password. Quando verrà aperto il voto riceverai una email con un pulsante di accesso.</p>
			<p>Puoi comunque sempre richiedere una email col pulsante di accesso direttamente dalla <a href="https://www.premioitalia.org">home page del sito</a>.</p>
			<p>I tuoi dati (nome, cognome, email e voti espressi) sono custoditi dal Comitato di gestione del Premio Italia. In qualunque momento tu voglia che vengano rimossi basta richiederlo a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
		</div>
		@endif

	</div>
</div>

@endsection