@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Calendario del Premio Italia {{ $annata->anno }}</h1>
	</div>
	<div class="row">
		<div class="col-sm-12">
@if ($annata->fase() == 'pre')
<p>Le votazioni <b>non sono ancora aperte</b>.</p>
@elseif  ($annata->fase() == 'spoglio1')
<p>È terminata la prima fase: <b>è in corso lo spoglio dei voti</b>. La seconda fase aprirà il {{ Cutter::date($annata->fase_2_da, '%e %B') }}.</p>
@elseif  ($annata->fase() == 'spoglio2')
<p><b>È terminata la seconda fase</b>, grazie a tutti. I finalisti verranno resi noti il {{ Cutter::date($annata->finalisti, '%e %B') }}.</p>
@endif
<p>Ecco le date delle varie fasi:</p>
@include('common.tabella_fasi', [ 'annata' => $annata ])
		</div>
	</div>
	
@endsection
