@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Premio Italia {{ $annata->anno }}</h1>
	</div>

@if(Config::get('premioitalia.stato') == false) 
	<div class="row">
		<div class="col-sm-12">
			<p>La procedura di voto è <strong>momentaneamente sospesa</strong> per verifica di alcune segnalazioni sulla ammissibilità dei finalisti.</p>
		</div>
	</div>
@else

@if (in_array($annata->fase(), [ 'pre','spoglio1','spoglio2' ]))
	<div class="row">
		<div class="col-sm-12">
@if ($annata->fase() == 'pre')
<p>Le votazioni <b>non sono ancora aperte</b>.</p>
@elseif  ($annata->fase() == 'spoglio1')
<p>È terminata la prima fase: <b>è in corso lo spoglio dei voti</b>. La seconda fase aprirà il {{ Cutter::date($annata->fase_2_da, '%e %B') }}.</p>
@elseif  ($annata->fase() == 'spoglio2')
<p><b>È terminata la seconda fase</b>, grazie a tutti. I vincitori verranno resi noti il {{ Cutter::date($annata->premiazione, '%e %B') }}.</p>
@endif
<p>Ecco le date delle varie fasi:</p>
@include('common.tabella_fasi', [ 'annata' => $annata ])
		</div>
	</div>
@endif
	
@if (in_array($annata->fase(), [ 'fase1','fase2' ]))
	<div class="row">
		<div class="col-sm-12">
@if ($annata->fase() == 'fase1')
<p>È aperta la <b>prima fase delle votazioni: la segnalazione</b> di candidati.</p>
<ul>
	<li><b>Hanno già votato:</b> {{ $annata->voti()->whereFase('fase1')->whereStato('inviato')->count() }} elettori</li>
	<li>per un totale di {{ $annata->segnalazioni()->count() }} <b>segnalazioni</b></li>
	<li>e altri {{ $annata->voti()->whereFase('fase1')->whereStato('preparazione')->count() }} voti sono stati <b>inseriti ma non ancora inviati</b></li>
	<li>Al termine della prima fase <span data-countdown="{{ $annata->fase_1_a }}T23:59:59">mancano…</span></li>
</ul>
@elseif  ($annata->fase() == 'fase2')
<p>È aperta la <b>seconda fase delle votazioni: la votazione dei finalisti</b>.</p>
<ul>
	<li><b>Hanno già votato:</b> {{ $annata->voti()->whereFase('fase2')->whereStato('inviato')->count() }} elettori</li>
	<li>e altri {{ $annata->voti()->whereFase('fase2')->whereStato('preparazione')->count() }} voti sono stati <b>inseriti ma non ancora inviati</b></li>
	<li>Al termine della seconda fase <span data-countdown="{{ $annata->fase_2_a }}T23:59:59">mancano…</span></li>
</ul>
@endif
		</div>
	</div>


@auth 
	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title">Accedi al voto</h5>
					<p class="card-text">Ciao {{ $user->firstname }}, accedi al sistema di voto cliccando sul pulsante qui sotto. Troverai indicazioni sintetiche sulle regole nella pagina di voto stessa, ma se vuoi puoi anche <a href="{{ route('regolamento') }}">leggere il regolamento</a>.</p>
					<a class="btn btn-primary" href="{{ route('vote') }}">Accedi al voto</a>
				</div>
			</div>		
		</div>
	</div>	
@else
	<div class="row">
		<div class="col-md-6 col-sm-12">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title">Richiedi invito al voto</h5>
					<p class="card-text">Se sei già un elettore registrato, inserisci il tuo indirizzo email per ricevere il link di accesso al voto.</p>
					<form id="request-token">
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="basic-addon1">@</span>
						</div>
						<input id="request-token-email" type="text" class="form-control" placeholder="Indirizzo email" aria-label="Email" aria-describedby="basic-addon1">
						<div class="invalid-feedback" id="request-token-feedback">Errore</div>
						<div class="valid-feedback">Email inviata!</div>
					</div>
					<button class="btn btn-primary" type="submit">Richiedi invito</button>
					</form>
				</div>
			</div>		
		</div>

		<div class="col-md-6 col-sm-12">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title">Richiedi l'iscrizione al voto</h5>
					<p class="card-text">Non sei un elettore registrato? Se hai partecipato a un'Italcon o a una delle convention affiliate (Stranimondi, Deepcon o Aetnacon) puoi richiedere l'iscrizione.</p>
					<p class="card-text">Invia una mail a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a> indicando nome, cognome e convention alla quale hai partecipato, allegando la foto del badge o altre prove della tua partecipazione.</p>
				</div>
			</div>		
		</div>
	</div>	

@endauth

@endif



@if ($annata->fase() == 'finalisti')
@endif

@if ($annata->fase() == 'post')
<p>Le votazioni per il Premio Italia {{ $annata->anno }} sono terminate. <b>Grazie a tutti, ci vediamo l'anno prossimo</b>.</p>
@endif

@endif

@if ($annata->candidatureAperte())

	<div class="row">

		<div class="col-lg-12 col-md-11">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title"><a href="{{ route('candidature') }}">Sono aperte le candidature spontanee</a></h5>
					<p class="card-text">Se vuoi proporre un'opera o una candidatura per il Premio Italia puoi farlo fino al  {{ Cutter::date($annata->candidature_a, '%e %B') }}. <a href="{{ route('candidature') }}">Vai »</a></p>
				</div>
			</div>		
		</div>
	</div>
	
@endif

@if ($annata->fase() == 'fase')

	<div class="row">

		<div class="col-lg-12 col-md-11">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title"><a href="{{ route('candidature') }}">Consulta le candidature spontanee</a></h5>
					<p class="card-text">Il termine per inserire candidature è scadute, ma puoi consultare quelle inserite.. <a href="{{ route('candidature') }}">Vai »</a></p>
				</div>
			</div>		
		</div>
	</div>
	
@endif

	<div class="row">

		<div class="col-lg-12 col-md-11">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title">L'Italcon e la cerimonia del Premio Italia 2020 saranno online il 20 giugno 2020</h5>
					<p class="card-text">Appena saranno disponibili pubblicheremo qui il link per collegarsi e per l'iscrizione (opzionale).</p>
				</div>
			</div>		
		</div>
	</div>

	<div class="row">

		<div class="col-lg-3 col-md-6">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title"><a href="{{ route('storia') }}">Cos'è il Premio Italia</a></h5>
					<p class="card-text">La storia del premio italiano della fantascienza e del fantastico. <a href="{{ route('storia') }}">Leggi »</a></p>
				</div>
			</div>		
		</div>

		<div class="col-lg-3 col-md-6">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title"><a href="{{ route('albo') }}">L'albo d'oro</a></h5>
					<p class="card-text">L'elenco completo, per anno e per categoria, dei premi assegnati dal 1972 a oggi. <a href="{{ route('albo') }}">Consulta »</a></p>
				</div>
			</div>		
		</div>

		<div class="col-lg-3 col-md-6">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title"><a href="{{ route('italcon') }}">Italcon e le altre</a></h5>
				<p class="card-text">Scopri l'elenco completo delle <a href="{{ route('italcon') }}">Italcon</a> e delle convention affiliate al Premio.</a></p>
					</div>
			</div>		
		</div>

		<div class="col-lg-3 col-md-6">
			<div class="card" style="margin: 0.5em 0.5em 0.5em 0">
				<div class="card-body">
					<h5 class="card-title"><a href="{{ route('comecandidarsi') }}">Come ci si candida</a></h5>
					<p class="card-text">In poche parole non ci si candida, si viene candidati. <a href="{{ route('comecandidarsi') }}">Ecco come funziona</a></p>
				</div>
			</div>		
		</div>

	</div>	

@endsection
