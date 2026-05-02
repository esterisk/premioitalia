@extends('errors.layout')

@php
  $error_number = 429;
@endphp

@section('title')
  Troppe richieste.
@endsection

@section('description')
  @php
    $default_error_message = "Per favore <a href='javascript:history.back()''>torna indietro</a> e riprova, o ritorna alla <a href='".url('')."'>pagina principale</a>.";
  @endphp
  {!! isset($exception)? ($exception->getMessage()?e($exception->getMessage()):$default_error_message): $default_error_message !!}
@endsection
