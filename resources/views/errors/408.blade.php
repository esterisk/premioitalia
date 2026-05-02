@extends('errors.layout')

@php
  $error_number = 408;
@endphp

@section('title')
  Timeout della richiesta.
@endsection

@section('description')
  @php
    $default_error_message = "Per favore <a href='javascript:history.back()''>torna indietro</a>, aggiorna la pagina e riprova.";

  @endphp
  {!! isset($exception)? ($exception->getMessage()?e($exception->getMessage()):$default_error_message): $default_error_message !!}
@endsection
