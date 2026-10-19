@extends('emails.base')

@section('title')
Benvenuto fra gli elettori del Premio Italia
@endsection

@section('main')
<p>Caro {{ $user->firstname }},</p>
<p>la tua richiesta di iscrizione è stata approvata: sei ora registrato fra gli elettori del Premio Italia.</p>
<p>Quando le votazioni saranno aperte riceverai via email il link per votare; potrai anche richiederlo dalla home page del sito <a href="https://www.premioitalia.org/">https://www.premioitalia.org/</a>.</p>
<p>Se hai domande sul Premio Italia puoi scrivere a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
<p><br></p>
<p>Distinti saluti</p>
<p>L’Organizzazione del Premio Italia</p>
@endsection
