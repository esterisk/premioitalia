@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Premio Italia {{ $annata->anno }} - Voto seconda fase: votazione finale</h1>
	</div>
	
@if ($stato == 'preparazione')

	<div class="row">
		<div class="col-sm-12">
<p>È aperta la <b>seconda fase delle votazioni: la votazione finale</b> dei candidati finalisti. Il voto è aperto fino alla mezzanotte {{ Cutter::date($annata->fase_2_a, '%e %B') }}.</p>
<p>Per ogni categoria puoi scegliere fino a tre candidati che consideri i più meritevoli. Se pensi che nessuno dei finalisti sia meritevole e che il premio non debba essere assegnato, scegli «Non assegnare il premio per questa categoria». Se non conosci i candidati e preferisci non votare per una categoria, scegli «Non voto per questa categoria».</p>
<p>Clicca in sequenza sui candidati per selezionare prima scelta, seconda scelta e terza scelta. Clicca più volte sullo stesso candidato per farlo avanzare tra le tue scelte o per toglierlo dalle scelte.</p>
<p>Su <a href="sistema-di-voto">questa pagina</a> c'è una spiegazione dettagliata del meccanismo di voto.</p>
		</div>
	</div>
	
	<form id="vote-form" action="{{ route('vote') }}" method="post">
		{{ csrf_field() }}
		<input type="hidden" name="anno" value="{{ $annata->anno }}">
		<input type="hidden" name="fase" value="fase2">
		<input type="hidden" name="action" value="save">
		
		<button type="submit" class="btn btn-primary btn-save btn-save-floating">Salva</button>
		<div class="alert alert-success fixed-bottom save-alert">Voto salvato! Potrai tornare a modificarlo quando vuoi, ma quando hai finito <b>ricordati di inviarlo</b>. Se vuoi <button type="button" class="btn btn-danger btn-send-confirm">Invialo adesso</button></div>
		<div class="alert alert-danger fixed-bottom save-alert"></div>

	<div class="row row-categoria row-identita sticky-top">
		<div class="col-sm-12">
			<div class="custom-control custom-checkbox identity-confirm">
				<input class="custom-control-input" id="confirm" type="checkbox" value="1" name="confirm-{{ $user->id }}">
				<label class="custom-control-label" for="confirm">
				    Confermo sotto la mia responsabilità di essere <b>{{ $user->name }}</b>
				</label>
			</div>
		</div>
	</div>

<style>
.finalisti button.choice {
	border-radius:15px;
	font-size:12pt;
	font-weight:bold;
	border: none;
	position:absolute;
	width:30px;
	line-height:32px;
	text-align:center;
	height:30px;
	top:4px;
	color: white;
	padding: 0;
	background: #999;
	opacity: .3; 
}
.finalisti button.choice-1 { right: 75px; }
.finalisti button.choice-2 { right: 40px; }
.finalisti button.choice-3 { right: 5px; }
.finalisti button.choice-1.active { opacity: 1; background: #090; }
.finalisti button.choice-2.active { opacity: 1; background: #6C6; }
.finalisti button.choice-3.active { opacity: 1; background: #CC6; }
</style>

@foreach ($categorie as $cat)
	<div class="row row-categoria">
		<div class="col-sm-12">
			<h2>{{ $cat->nome }}</h2>
			<p class="definizione">{!! str_replace('periodo', ($annata->anno - 1), $cat->definizione) !!}</p>
			<div class="finalisti" data-cat="{{ $cat->slug }}">
				<input type="hidden" name="{{ $slug = $cat->slug }}[1]" value="{{ $user->choice($voto, $slug, 1) }}">
				<input type="hidden" name="{{ $slug = $cat->slug }}[2]" value="{{ $user->choice($voto, $slug, 2) }}">
				<input type="hidden" name="{{ $slug = $cat->slug }}[3]" value="{{ $user->choice($voto, $slug, 3) }}">
				<p>{{ $slug }} {{ $user->choice($voto, $slug, 1) }}</p>
				@foreach ($cat->finalisti($annata->anno)->get()->shuffle() as $candidato)
				<div style="position:relative">
				<button type="button" class="label btn btn-block btn-outline-primary" data-value="{{ $candidato->id }}" data-cat="{{ $cat->slug }}">{{ $candidato->descrizione }}</button>
				<button type="button" class="activable choice choice-1 {{ ($user->choice($voto, $slug, 1) == $candidato->id) ? ' active' : ''}}" data-value="{{ $candidato->id }}" data-cat="{{ $cat->slug }}" data-position="1" title="Prima scelta">1</button>
				<button type="button" class="activable choice choice-2 {{ ($user->choice($voto, $slug, 2) == $candidato->id) ? ' active' : ''}}" data-value="{{ $candidato->id }}" data-cat="{{ $cat->slug }}" data-position="2" title="Seconda scelta">2</button>
				<button type="button" class="activable choice choice-3 {{ ($user->choice($voto, $slug, 3) == $candidato->id) ? ' active' : ''}}" data-value="{{ $candidato->id }}" data-cat="{{ $cat->slug }}" data-position="3" title="Terza scelta">3</button>
				</div>
				@endforeach
				<div style="position:relative">
				<button type="button" class="activable btn btn-block btn-outline-danger{{ ($user->choice($voto, $slug, 1) === 'N') ? ' active' : ''}}" data-value="N" data-cat="{{ $cat->slug }}">Non assegnare il premio per questa categoria</button>
				</div>
				<div style="position:relative">
				<button type="button" class="activable btn btn-block btn-outline-secondary{{ ($user->choice($voto, $slug, 1) === 0 || $user->choice($voto, $slug, 1) == 'U') ? ' active' : ''}}" data-value="U" data-cat="{{ $cat->slug }}">Non voto per questa categoria</button>
				</div>
			</div>
		</div>
	</div>
@endforeach

	<div class="row">
		<div class="col-sm-12">
			<p>Quando hai inserito tutte le segnalazioni e ritieni di essere pronto a inviare il tuo voto, clicca il pulsante « <b>Invia il voto</b> ».</p>
			<p>Ricorda che, una volta inviato, il voto <b>non sarà più modificabile</b>.</p>
			<p>
				<button type="submit" class="btn btn-primary btn-save btn-save">Salva per continuare in seguito</button>
				<button type="button" class="btn btn-danger btn-send-confirm">Invia il voto</button>
			</p>
		</div>
	</div>
	</form>

<!-- Modal -->
<div class="modal fade" id="confirmSend" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Invia il tuo voto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <b>ATTENZIONE:</b> Una volta inviato il voto non sarà più modificabile. Sei sicuro di volerlo inviare adesso?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Non ancora</button>
        <button type="button" class="btn btn-primary btn-send-confirm">Invia adesso</button>
      </div>
    </div>
  </div>
</div>


@else 
	<div class="row">
		<div class="col-sm-12">
			<div class="alert alert-success">
				Ha inviato il tuo voto alle {{ Cutter::date($sent_at, '%H:%M del %e %B') }}.<br/>
				Grazie! I risultati saranno pubblicati la sera del {{ Cutter::date($annata->premiazione, '%e %B') }}.
			</div>
		</div>
	</div>
@endif


@endsection
