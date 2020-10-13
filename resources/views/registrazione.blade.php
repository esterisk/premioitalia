@if($status)

	<div class="row">
		<h1 class="col-sm-12">Ammissione nuovi elettori</h1>
	</div>
	
	<div class="row">
		@if($status == 'WRONGCODE')
		<p class="col-sm-12 alert alert-danger">
			Il codice non sembra corretto. Verifica che nome, cognome, email e codice siano gli stessi riportati nella mail che hai ricevuto. Se ritieni che i dati non fossero precisi puoi richiedere la correzione dei dati a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.
		</p>
		@endif
		
		@if($status == 'CODENOTFOUND')
		<p class="col-sm-12 alert alert-danger">
			Il codice non è stato trovato tra quelli segnalati dagli organizzatori della convention. Verifica che nome, cognome, email e codice siano gli stessi riportati nella mail che hai ricevuto.
		</p>
		@endif
		
		@if($status == 'VOTERFOUND')
		<div class="col-sm-12 alert alert-success">
			<h4 class="alert-heading">Già iscritto!</h4>
			Risulti già registrato tra i votanti: a posto così! Presto riceverai la mail di invito al voto.
		</div>
		@endif
		
		@if($status == 'EMAILUSED')
		<p class="col-sm-12 alert alert-danger">
			La tua iscrizione è valida, ma occorre che indichi un indirizzo email diverso perché questo è già usato da un altro elettore.
		</p>
		@endif
		
		@if($status == 'NAMEFOUND')
		<p class="col-sm-12 alert alert-danger">
			Un elettore con questo nome e cognome risulta già iscritto, con indirizzo email «{{ maskemail($elettore['email']) }}». Se è un omonimo, o se sei tu e l'indirizzo email non è più valido, per favore scrivi a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a> per segnalarlo.
		</p>
		@endif
				
		@if($status == 'SAVED')
		<div class="col-sm-12 alert alert-success">
			<h4 class="alert-heading">Elettore inserito!</h4>
			<p>A posto così, grazie! Presto riceverai la mail di invito al voto.</p>
		</div>
		@endif
				
		@if($status == 'VERIFICATIONSENT')
		<div class="col-sm-12 alert alert-success">
			<h4 class="alert-heading">Ti abbiamo inviato un nuovo invito all'iscrizione</h4>
			<p>Ti abbiamo mandato un nuovo invito a iscriverti al nuovo indirizzo che hai specificato, per verificare che fosse esatto. Dovresti riceverlo entro pochi istanti. Se non lo ricevi, verifica l'indirizzo che hai inserito a riprova.</p>
		</div>
		@endif
				
	</div>

@endif

@if(in_array($status, [ 'REQUEST','WRONGCODE','CODENOTFOUND','VERIFICATION' ]))

	<div class="row">
		<div class="col-sm-12">
		<p>Può essere ammesso al voto al Premio Italia chi ha partecipato a un'Italcon, l'anno scorso o in anni precedenti, o a una convention affiliata.</p>
		<p>Se hai partecipato a una di queste convention, dovresti aver ricevuto dall'organizzazione della convention una email contenente un codice per registrarti su questo sito. In questo caso inserisci qui sotto i dati presenti nella mail.</p>
		<p>Se non l'hai ricevuta puoi chiedere all'organizzazione della convention di inviartela, oppure contattare direttamente via email lo <a href="mailto:staff@premiotalia.org">staff del Premio Italia</a>, indicando a quale convention hai partecipato e possibilmente fornendo qualche prova (per es. la scansione o la foto del badge, foto della tua presenza in loco, oppure indicare persone che possono garantire per te).</p>
		</div>
	</div>

@endif

@if(in_array($status, [ 'REQUEST','WRONGCODE','CODENOTFOUND','EMAILUSED','VERIFICATION','VERIFICATIONSENT' ]))

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
@if(in_array($status, [ 'EMAILUSED','VERIFICATION','VERIFICATIONSENT' ]))
		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="email">Email indicata</label>
			<div class="col-sm-6">
				<input type="hidden" name="email" placeholder="email" value="{{ $dati['email'] }}">
				<input type="text" class="form-control disabled" placeholder="email" value="{{ $dati['email'] }}" disabled="disabled">
				@if(!empty($errore['email']))
					<div class="invalid-feedback">{{ $errore['email'] }}</div>
				@endif
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="email-new">Email da usare</label>
			<div class="col-sm-6">
				<input type="text" class="form-control{{ (!empty($errore['email2'])?' is-invalid':'') }}" name="email2" placeholder="email da usare" value="{{ $dati['email2'] }}">
				@if(!empty($errore['email2']))
					<div class="invalid-feedback">{{ $errore['email2'] }}</div>
				@endif
			</div>
		</div>
@else
		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="email">Email</label>
			<div class="col-sm-6">
				<input type="text" class="form-control{{ (!empty($errore['email'])?' is-invalid':'') }}" name="email" placeholder="email" value="{{ $dati['email'] }}">
				@if(!empty($errore['email']))
					<div class="invalid-feedback">{{ $errore['email'] }}</div>
				@endif
			</div>
		</div>
@endif
		<div class="form-group row">
			<label class="col-sm-2 col-form-label text-right" for="codice">Codice</label>
			<div class="col-sm-6">
				<input type="text" class="form-control{{ (!empty($errore['codice'])?' is-invalid':'') }}" name="codice" placeholder="codice" value="{{ $dati['codice'] }}">
				@if(!empty($errore['codice']))
					<div class="invalid-feedback">{{ $errore['codice'] }}</div>
				@endif
			</div>
		</div>

		<div class="form-group row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-primary">Invia</button>
			</div>
		</div>		
	</form>

@endif
