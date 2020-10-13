@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Richiesta registrazione nuovo elettore</h1>
	</div>
	
	<div class="row">
		<p class="col-sm-12">
			Puoi richiedere l'iscrizione tra i votanti del Premio Italia se hai partecipato a un'Italcon o a una convention affiliata al Premio Italia. Compila il modulo qui sotto, e carica la foto di un badge della convention a cui hai partecipato, o una foto o altro che dimostri la tua partecipazione. La domanda sarà valutata quanto prima e riceverai una mail per completare la registrazione.
		</p>
	</div>
	
	<form method="post">
		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="nome">Nome</label>
			<div class="col-sm-6">
				<input type="text" class="form-control{{ (!empty($errore['nome'])?' is-invalid':'') }}" name="nome" placeholder="Nome" value="{{ $dati['nome'] }}">
				@if(!empty($errore['nome']))
					<div class="invalid-feedback">{{ $errore['nome'] }}</div>'
				@endif
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="cognome">Cognome</label>
			<div class="col-sm-6">
				<input type="text" class="form-control{{ (!empty($errore['cognome'])?' is-invalid':'') }}" name="cognome" placeholder="cognome" value="{{ $dati['cognome'] }}">
				@if(!empty($errore['cognome']))
					<div class="invalid-feedback">{{ $errore['cognome'] }}</div>
				@endif
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="email">Email</label>
			<div class="col-sm-6">
				<input type="hidden" name="email" placeholder="email" value="{{ $dati['email'] }}">
				<input type="text" class="form-control disabled" placeholder="email" value="{{ $dati['email'] }}">
				@if(!empty($errore['email']))
					<div class="invalid-feedback">{{ $errore['email'] }}</div>
				@endif
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="email">Convention alla quale hai partecipato</label>
			<div class="col-sm-6">
				<select name="convention" class="form-control{{ (!empty($errore['convention'])?' is-invalid':'') }}">
					<option value="">Seleziona la convention</option>
				@foreach ($conventions as $convention)
					<option value="{{ $convention->codice }}">{{ $convention->anno }}: {{ $convention->titolo_edizione }} ({{ $convention->italcon ? 'Italcon '.$convention->italcon : 'affiliata' }})</option>
				@endforeach
				</select>
				@if(!empty($errore['convention']))
					<div class="invalid-feedback">{{ $errore['convention'] }}</div>
				@endif
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="allegato">Allegato</label>
			<div class="col-sm-6">
				<input type="file" class="form-control" name="allegato">
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="codice">Messaggio</label>
			<div class="col-sm-6">
				<textarea class="form-control" rows="5" name="messaggio">{{ $dati['messaggio'] }}</textarea>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-primary">Invia</button>
			</div>
		</div>		
	</form>

@endsection
