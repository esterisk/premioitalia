@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Vuoi fare una donazione al Premio Italia?</h1>
	</div>
	@include('common.donazione')
	<div class="row row-regolamento">
		<div class="col-sm-12">
			<p>Oppure, <a href="{{ route('home') }}">torna all'home page</a></p>
		</div>	
	</div>
	
@endsection
