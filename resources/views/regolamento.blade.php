@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Premio Italia: regolamento</h1>
	</div>
	<div class="row row-download">
		<p><a href="Regolamento-Premio-Italia-1.3.pdf"><img src="/images/pdf.png"> Scarica il regolamento in pdf</a></p>
	</div>
	<div class="row row-regolamento">
		<div class="col-sm-12">
			@include('regolamento.regolamento-'.$versione)
		</div>	
	</div>
	
@endsection
