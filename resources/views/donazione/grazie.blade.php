@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Grazie!</h1>
	</div>
	<div class="row row-regolamento">
		<div class="col-sm-12">
			<p>Grazie infinite per la tua donazione! Il Premio Italia è il premio della comunità degli appassionati, e le donazioni sono la testimonianza che la comunità è viva e solidale!</p>
			<p><a href="{{ route('home') }}">Torna all'home page</a></p>
		</div>	
	</div>
	
@endsection
