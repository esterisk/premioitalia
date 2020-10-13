@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Premio Italia {{ $annata->anno }} - Voto seconda fase: votazione finale</h1>
	</div>
	
@if ($stato == 'preparazione')

	<div class="row">
		<div class="col-sm-12">
<p>È aperta la <b>seconda fase delle votazioni: la votazione finale</b> dei candidati finalisti. Il voto è aperto fino alla mezzanotte {{ Cutter::date($annata->fase_2_a, '%e %B') }}.</p>
<p>Per ogni categoria scegli il candidato che consideri più meritevole. Se pensi che nessuno dei finalisti sia meritevole e che il premio non debba essere assegnato, scegli «Non assegnare il premio per questa categoria». Se non conosci i candidati e preferisci non votare per una categoria, scegli «Non voto per questa categoria».</p>
		</div>
	</div>
	
	<form id="vote-form" action="{{ route('vote') }}" method="post">
		{{ csrf_field() }}
		<input type="hidden" name="anno" value="{{ $annata->anno }}">
		<input type="hidden" name="fase" value="fase2">
		<input type="hidden" name="action" value="save">
		
		<button type="submit" class="btn btn-primary btn-save btn-save-floating">Salva</button>
		<div class="alert alert-success fixed-bottom save-alert">Voto salvato! Potrai tornare a modificarlo quando vuoi, ma quando hai finito ricordati di inviarlo!</div>
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

@foreach ($categorie as $cat)
	<div class="row row-categoria">
		<div class="col-sm-12">
			<h2>{{ $cat->nome }}</h2>
			<p class="definizione">{!! str_replace('periodo', ($annata->anno - 1), $cat->definizione) !!}</p>
			<div class="finalisti">
				<input type="hidden" name="{{ $slug = $cat->slug }}" value="{{ !empty($voto) ? $voto->$slug : 0 }}">
				@foreach ($cat->finalisti($annata->anno)->get()->shuffle() as $candidato)
				<button type="button" class="btn btn-block btn-outline-primary{{ !empty($voto) && ($voto->$slug == $candidato->id) ? ' active' : ''}}" data-value="{{ $candidato->id }}" data-cat="{{ $cat->slug }}">{{ $candidato->descrizione }}</button>
				@endforeach
				<button type="button" class="btn btn-block btn-outline-danger{{ !empty($voto) && ($voto->$slug == 'N') ? ' active' : ''}}" data-value="N" data-cat="{{ $cat->slug }}">Non assegnare il premio per questa categoria</button>
				<button type="button" class="btn btn-block btn-outline-secondary{{ empty($voto) || ($voto->$slug == 0) ? ' active' : ''}}" data-value="0" data-cat="{{ $cat->slug }}">Non voto per questa categoria</button>
			</div>
		</div>
	</div>
@endforeach

	<div class="row">
		<div class="col-sm-12">
			<p>Quando hai inserito tutte le segnalazioni e ritieni di essere pronto a inviare il tuo voto, clicca il pulsante « <b>Invia il voto</b> ».</p>
			<p>Ricorda che, una volta inviato, il voto non sarà più modificabile.</p>
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
        Una volta inviato il voto non sarà più modificabile. Sei sicuro di volerlo inviare adesso?
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
