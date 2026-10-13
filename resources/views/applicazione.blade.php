@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Richiesta di iscrizione al voto</h1>
	</div>

	@if(session('applicazione_inviata'))
		<div class="row">
			<div class="col-sm-12">
				<div class="alert alert-success">
					La tua richiesta è stata registrata e sarà valutata al più presto. Ti abbiamo inviato un'email di conferma: se non la trovi, controlla nella cartella spam.
				</div>
			</div>
		</div>
	@endif

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

		<form method="post" action="{{ route('applicazione-post') }}" enctype="multipart/form-data" id="form-applicazione">
			@csrf
			<input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
			<input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

			<div class="form-group row">
				<div class="col-sm-12">
					<div class="alert alert-info" id="geo-stato">Verifica della posizione in corso&hellip;</div>
				</div>
			</div>

			<div class="form-group row mt-3">
				<label class="col-sm-2 col-form-label text-right" for="nome">Nome</label>
				<div class="col-sm-6">
					<input type="text" class="form-control{{ $errors->has('nome') ? ' is-invalid' : '' }}" id="nome" name="nome" placeholder="Nome" value="{{ old('nome') }}" required>
					@error('nome')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<div class="form-group row mt-3">
				<label class="col-sm-2 col-form-label text-right" for="cognome">Cognome</label>
				<div class="col-sm-6">
					<input type="text" class="form-control{{ $errors->has('cognome') ? ' is-invalid' : '' }}" id="cognome" name="cognome" placeholder="Cognome" value="{{ old('cognome') }}" required>
					@error('cognome')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<div class="form-group row mt-3">
				<label class="col-sm-2 col-form-label text-right" for="email">Email</label>
				<div class="col-sm-6">
					<input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
					@error('email')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<div class="form-group row{{ $selfieSempreRichiesto || $errors->has('selfie') ? '' : ' d-none' }} mt-3" id="blocco-selfie">
				<label class="col-sm-2 col-form-label text-right" for="selfie">Foto</label>
				<div class="col-sm-6">
					<input type="file" class="form-control-file{{ $errors->has('selfie') ? ' is-invalid' : '' }}" id="selfie" name="selfie" accept="image/*" capture="user">
					<small class="form-text text-muted">Scatta una foto che ritragga te insieme al cartello del Premio Italia.</small>
					@error('selfie')
						<div class="invalid-feedback d-block">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<div class="form-group row mt-3">
				<div class="col-sm-10 offset-sm-2">
					<button type="submit" class="btn btn-primary">Invia la richiesta</button>
				</div>
			</div>
		</form>

		<script>
			(function () {
				var sede = @json($convention->hasLocation() ? ['lat' => $convention->latitudine(), 'lng' => $convention->longitudine()] : null);
				var raggio = {{ $raggio }};
				var selfieSempreRichiesto = @json((bool) $selfieSempreRichiesto);

				var stato = document.getElementById('geo-stato');
				var bloccoSelfie = document.getElementById('blocco-selfie');
				var campoLat = document.getElementById('latitude');
				var campoLng = document.getElementById('longitude');

				function messaggio(testo, classe) {
					stato.className = 'alert ' + classe;
					stato.innerHTML = testo;
				}

				function chiediSelfie(testo) {
					bloccoSelfie.classList.remove('d-none');
					messaggio(testo + ' Per completare la richiesta scatta una foto che ritragga te e il cartello del Premio Italia.', 'alert-warning');
				}

				function distanza(lat1, lng1, lat2, lng2) {
					var r = 6371000;
					var dLat = (lat2 - lat1) * Math.PI / 180;
					var dLng = (lng2 - lng1) * Math.PI / 180;
					var a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
						+ Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
					return r * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
				}

				if (!sede) {
					chiediSelfie('Per questa convention non è disponibile la verifica della posizione.');
					return;
				}

				if (!navigator.geolocation) {
					chiediSelfie('Il tuo browser non permette di rilevare la posizione.');
					return;
				}

				navigator.geolocation.getCurrentPosition(function (posizione) {
					campoLat.value = posizione.coords.latitude;
					campoLng.value = posizione.coords.longitude;

					var metri = distanza(sede.lat, sede.lng, posizione.coords.latitude, posizione.coords.longitude);

					if (metri > raggio) {
						chiediSelfie('Risulti a più di ' + Math.round(metri) + ' metri dalla convention.');
					} else if (selfieSempreRichiesto) {
						chiediSelfie('Da questo dispositivo è già stata inviata un\'altra richiesta.');
					} else {
						bloccoSelfie.classList.add('d-none');
						messaggio('Posizione confermata: risulti alla convention.', 'alert-success');
					}
				}, function () {
					campoLat.value = '';
					campoLng.value = '';
					chiediSelfie('Non è stato possibile rilevare la tua posizione.');
				}, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
			})();
		</script>

	@endif
@endsection
