@extends('errors.layout')

@php
	$error_number = 500;
@endphp

@section('title')
	Non è colpa tua, è colpa mia.
@endsection

@section('description')
	@php
	  $default_error_message = "Si è verificato un errore interno del server. Se l'errore persiste, contatta il team di sviluppo.";
	@endphp
	{!! isset($exception)? ($exception->getMessage()?e($exception->getMessage()):$default_error_message): $default_error_message !!}
@endsection
