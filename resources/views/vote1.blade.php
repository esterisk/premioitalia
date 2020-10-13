@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Premio Italia {{ $annata->anno }} - Voto prima fase: segnalazione finalisti</h1>
	</div>
	
@if ($stato == 'preparazione')

	<div class="row">
		<div class="col-sm-12">
<p>È aperta la <b>prima fase delle votazioni: la segnalazione</b> di candidati relativi al periodo di riferimento del {{ $annata->anno - 1 }}. Il voto è aperto fino alla mezzanotte {{ Cutter::date($annata->fase_1_a, '%e %B') }}.</p>
		</div>
	</div>
	
	<form id="vote-form" action="{{ route('vote') }}" method="post">
		{{ csrf_field() }}
		<input type="hidden" name="anno" value="{{ $annata->anno }}">
		<input type="hidden" name="fase" value="fase1">
		<input type="hidden" name="action" value="save">
		
		<button type="submit" class="btn btn-primary btn-save btn-save-floating">Salva</button>
		<div class="alert alert-success fixed-bottom save-alert">Voto salvato! Potrai tornare a modificarlo quando vuoi, ma quando hai finito <b>ricordati di inviarlo</b>!</div>
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
			<p class="definizione">{!! str_replace('periodo', ($annata->anno - 1), $cat->definizione) !!} 
				<button class="btn btn-link btn-sm rule-detail">Dettagli</button></p>
			@if($cat->esclusi_ultimi > 0) <p>Non sono candidabili i vincitori delle ultime {{ $cat->esclusi_ultimi }} edizioni:</p><ul>
				@foreach ($cat->ultimiVincitori($annata->anno) as $candidato)
					<li>{{ $candidato->descrizione }} ({{ $candidato->anno }})</li>
				@endforeach
				</ul>	 
			@endif

			<div class="detail" style="display:none">
				<dl>
				@foreach($cat->regole as $regola)
					<dt>{{ $regola->titolo }}</dt>
					<dd>{{ $regola->testo }}</dd>
				@endforeach
				</dl>
			</div>
			<div class="candidature-env">
				<p><a class="btn btn-secondary btn-sm" data-toggle="collapse" href="#candidature-{{ $cat->slug }}" role="button" aria-expanded="false" aria-controls="">Mostra candidature spontanee</a></p>
				<div class="collapse" id="candidature-{{ $cat->slug }}">
					<div class="card card-body"><ul class="insert-candidature">
					@foreach ($cat->candidature()->valide()->get()->shuffle() as $candidatura)
						<li><a href="#" data-categoria="{{ $cat->slug }}" data-candidatura="{{ $candidatura->campi }}"><i class="fas fa-arrow-circle-down"></i>{!! $candidatura->descrizione_ricca() !!}</a></li>
					@endforeach
					</ul>
					<p><small>Le candidature sono in ordine casuale ogni volta diverso. La validità della candidatura è verificata ma non assicurata.<br/>
						Le candidature spontanee sono solo suggerimenti proposti da autori e lettori. L'elettore è libero di inserire segnalazioni non comprese tra le candidature spontanee.</small></p>
					</div>
				</div>
			</div>
			<div class="segnalazioni">
				<table class="table">
				<tbody>
					<tr>
						<th class="letter"></th>
						@foreach ($cat->campi as $c)
						<th>{{ Config::get('premioitalia.campi.'.$c) }}</th>
						@endforeach
					</tr>
					@for ($i=1; $i<=\Config::get('premioitalia.numero_segnalazioni'); $i++)
					<tr>
						<th class="letter">{{ substr("ABCDEF",$i-1,1) }}</th>
						@foreach ($cat->campi as $c)
							<?php $label = $cat->fieldLabel($i,$c); ?>
						<td><input type="text" data-riga-segnalazione="{{ $cat->slug }}-{{ $i }}" class="form-control" value="{{ !empty($voto) ? $voto->$label : '' }}" title="{{ Config::get('premioitalia.campi.'.$c) }}" placeholder="{{ Config::get('premioitalia.campi.'.$c) }}" name="{{ $label }}"></td>
						@endforeach
						<td width="20px"><a href="#" class="btn btn-light clear-segnalazione" data-clear="{{ $cat->slug }}-{{ $i }}"><i class="fas fa-times"></i><a></td>
					</tr>
					@endfor
				</tbody>
				</table>
			</div>
		</div>
	</div>
@endforeach

	<div class="row">
		<div class="col-sm-12">
			<p>Quando hai inserito tutte le segnalazioni e ritieni di essere pronto a inviare il tuo voto, clicca il pulsante « <b>Invia il voto</b> ».</p>
			<p>Ricorda che, una volta inviato, il voto <b>non sarà più modificabile</b>.</p>
			<p><button type="submit" class="btn btn-primary btn-save btn-save">Salva per continuare in seguito</button>   
			<button type="button" class="btn btn-danger btn-send-confirm">Invia il voto</button></p>
			<!--- data-toggle="modal" data-target="#confirmSend"-->
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
        <b>ATTENZIONE!</b> Una volta inviato il voto <b>non sarà più modificabile</b>. Sei sicuro di volerlo inviare adesso?
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
				Grazie! La seconda fase delle votazioni si aprirà il {{ Cutter::date($annata->fase_2_da, '%e %B') }}.
			</div>
		</div>
	</div>
@endif


@endsection
