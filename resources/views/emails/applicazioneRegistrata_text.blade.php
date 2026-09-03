@extends('emails.base_text')

@section('main')
Caro {{ $application->first_name }},

@if($convention)
abbiamo registrato la tua richiesta di iscrizione fra gli elettori del Premio Italia, presentata durante {{ $convention->titolo_edizione }}.
@else
abbiamo registrato la tua richiesta di iscrizione fra gli elettori del Premio Italia.
@endif

La richiesta sarà valutata al più presto: riceverai un'altra email non appena l'iscrizione sarà confermata.

Nel frattempo non è necessario fare nulla. Se non sei stato tu a presentare questa richiesta, segnalacelo scrivendo a staff@premioitalia.org.
@endsection
