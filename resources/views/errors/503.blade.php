@extends('errors.layout')

@php
  $error_number = 503;
@endphp

@section('title')
  Non è colpa tua, è colpa mia.
@endsection

@section('description')
  @php
    $default_error_message = "Il server è sovraccarico o in manutenzione. Per favore riprova più tardi.";
  @endphp
  {!! isset($exception)? ($exception->getMessage()?e($exception->getMessage()):$default_error_message): $default_error_message !!}
@endsection
