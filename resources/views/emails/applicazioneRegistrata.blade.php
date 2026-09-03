@extends('emails.base')

@section('title')
Richiesta di iscrizione al voto del Premio Italia
@endsection

@section('main')
<p>Caro {{ $application->first_name }},</p>
@if($convention)
<p>abbiamo registrato la tua richiesta di iscrizione fra gli elettori del Premio Italia, presentata durante {{ $convention->titolo_edizione }}.</p>
@else
<p>abbiamo registrato la tua richiesta di iscrizione fra gli elettori del Premio Italia.</p>
@endif
<p>La richiesta sarà valutata al più presto: riceverai un'altra email non appena l'iscrizione sarà confermata.</p>
<p>Nel frattempo non è necessario fare nulla. Se non sei stato tu a presentare questa richiesta, segnalacelo scrivendo a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
@endsection
