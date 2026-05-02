@extends('errors.layout')

@php
  $error_number = 403;
@endphp

@section('title')
  Accesso riservato.
@endsection

@section('description')
  @php
    $default_error_message = "Per favore <a href='javascript:history.back()''>torna indietro</a> o ritorna alla <a href='".url('')."'>pagina principale</a>.";
  @endphp
  {!! isset($exception)? ($exception->getMessage()?e($exception->getMessage()):$default_error_message): $default_error_message !!}
@endsection
