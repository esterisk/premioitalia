@extends('emails.base')

@section('title')
Il tuo voto - Premio Italia {{ $voto->anno }} - Fase 2
@endsection

@section('main')
<p>Caro {{ $user->name }},</p>
<p>Grazie per aver votato alla fase finale del Premio Italia {{ $voto->anno }}.</p>
<p>Ti riportiamo, a titolo di promemoria, i voti che hai espresso.</p>

<p><br></p>
<table><tbody><tr><th></th><td>
<?php $current = false; ?>
@foreach ($preferenze as $s)
	@if ($s->categoria->nome != $current)
	</td></tr>
	<tr>
	<th style="border-top: 1px solid #666; margin-top:15px; width:30%; vertical-align: top; text-align: left; font-size:80%;">{{ $current = $s->categoria->nome }}</th>
	<td style="border-top: 1px solid #666; margin-top:15px, vertical-align: top; text-align: left; font-size:80%;">
	@endif
	@if($s->candidato_1_id == -1) <p>Non assegnare il premio</p>
	@elseif($s->candidato_1_id == 0) <p>Nessuna scelta</p>
	@else
	<p>{{ $s->candidato_1->descrizione }} <i>(prima scelta)</i></p>
		@if($s->candidato_2_id > 0) <p>{{ $s->candidato_2->descrizione }} <i>(seconda scelta)</i></p> @endif
		@if($s->candidato_3_id > 0) <p>{{ $s->candidato_3->descrizione }} <i>(terza scelta)</i></p> @endif
	@endif
@endforeach
</td></tr></tbody></table>
<p><br></p>
<p>Il voto è stato registrato alle {{ Cutter::date($voto->sent_at, '%H:%M del %e %B') }}.</p>
@endsection