@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">{{ $page_title }}</h1>
	</div>
	<div class="row row-regolamento">
		<div class="col-sm-12">
		<?php $anno = false; ?>
		@foreach ($convention as $c) 
			@if ($anno != $c->anno)
			<h3>{{ $anno = $c->anno }}</h3>
			@endif
			<p>
			@auth
			<div class="btn-group btn-group-sm partecipazione" role="group">
				<button type="button" data-partecipazione="{{ $c->id }}" data-valore="yes" class="btn {{ $partecipazioni->contains($c->id) ? 'btn-success' : 'btn-light' }}">C'ero</button>
				<button type="button" data-partecipazione="{{ $c->id }}" data-valore="no" class="btn {{ $partecipazioni->contains($c->id) ? 'btn-light' : 'btn-secondary' }}">Non c'ero</button>
			</div>
			@endauth
			{!! $c->italcon ? '<b>Italcon '.$c->italcon.'</b> ' : '' !!}{{ $c->titolo_edizione }} - 
				{{ $c->city }} 
				{{ $c->date_start != '0000-00-00' ? 'dal '.Cutter::date($c->date_start, '%e %B %Y') : '' }} 
				{{ $c->date_end != '0000-00-00' ? 'al '.Cutter::date($c->date_end, '%e %B %Y') : '' }} 
				{!! ( $c->italcon > 0 || $c->serie->affiliata_da == 0 || $c->serie->affiliata_da > $c->anno ) ? '' : '<i>(affiliata)</i>' !!}</p>
		@endforeach
		</div>	
	</div>
	
@endsection
