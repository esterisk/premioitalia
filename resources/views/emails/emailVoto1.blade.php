@extends('emails.base')

@section('title')
Il tuo voto - Premio Italia {{ $voto->anno }} - Fase 1
@endsection

@section('main')
<p>Caro {{ $user->name }},</p>
<p>Grazie per aver votato alla prima fase del Premio Italia {{ $voto->anno }}.</p>
<p>Ti riportiamo, a titolo di promemoria, i voti che hai espresso.</p>
<p><br></p>
<table><tbody><tr><th></th><td>
<?php $current = false; ?>
@foreach ($segnalazioni as $s)
	@if ($s->categoria->nome != $current)
	</td></tr>
	<tr>
	<th style="border-top: 1px solid #666; margin-top:15px; width:30%; vertical-align: top; text-align: left; font-size:80%;">{{ $current = $s->categoria->nome }}</th>
	<td style="border-top: 1px solid #666; margin-top:15px, vertical-align: top; text-align: left; font-size:80%;">
	@endif
	<p>{{ $s->descrizione }}</p>
@endforeach
</td></tr></tbody></table>
<p><br></p>
<p>Il voto è stato registrato alle {{ Cutter::date($voto->sent_at, '%H:%M del %e %B') }}.</p>
@endsection