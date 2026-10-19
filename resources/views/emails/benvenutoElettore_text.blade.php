@extends('emails.base_text')

@section('main')
Caro {{ $user->firstname }},

la tua richiesta di iscrizione è stata approvata: sei ora registrato fra gli elettori del Premio Italia.

Quando le votazioni saranno aperte riceverai via email il link per votare; potrai anche richiederlo dalla home page del sito https://www.premioitalia.org/.

Se hai domande sul Premio Italia puoi scrivere a staff@premioitalia.org.

Distinti saluti
L’Organizzazione del Premio Italia
@endsection
