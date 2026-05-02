@extends('layouts.app')

@section('content')

@if($status == 'confirm')
<div class="row">
	<h1 class="col-sm-12">Se stato rimosso dai votanti dal Premio Italia</h1>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="alert alert-warning">
			Abbiamo rimosso la tua iscrizione ai votanti del Premio Italia.<br />
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<p>Abbiamo preso nota che non vuoi più ricevere email di avviso al voto del Premio Italia: non te ne manderemo più.</p>
		<p>Nel caso cambiassi idea, avvisaci scrivendo a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
	</div>
</div>

@elseif ($status == 'ask')

<div class="row">
	<h1 class="col-sm-12">Vuoi essere rimosso dai votanti dal Premio Italia?</h1>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="mb-3">
			Non riceverai più le nostre email per l'accesso al voto. In qualunque momento però potrai chiedere di essere abilitato nuovamente.<br />
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<form method="post" action="{{ route('unsubscribe-confirm') }}">
			@csrf
			<input type="hidden" name="uid" value="{{ $user->id }}">
			<input type="hidden" name="token" value="{{ $user->token }}">
			<button type="submit" class="btn btn-danger">Rimuovi</button>
		</form>
	</div>
</div>
@elseif ($status == 'error')

<div class="row">
	<p class="col-sm-12">Si è verificato un errore. Per completare la disiscrizione scrivi a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a></p>
</div>

@endif
@endsection