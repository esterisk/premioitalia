@extends('emails.base')

@section('title')
Conferma iscrizione al voto al premio Italia
@endsection

@section('main')
<p>Caro {{ $user->firstname }},</p>
<p>è stata richista l’iscrizione al voto al Premio Italia a nome “{{$richiedente}}" con questo indirizzo email.</p>
<p>Se l’iscrizione era a tuo nome, ti confermiamo che risulti già iscritto. A votazioni aperte riceverai l’accesso via email o potrai richiederlo dalla home page del sito <a href="https://www.premioitalia.org/">https://www.premioitalia.org/</a>.</p>
<p>Se la richiesta non era a tuo nome, tieni presente che ogni iscritto deve essere registrato con un proprio indirizzo email. Se vuoi, fai richiesta nuovamente specificando un indirizzo email diverso.</p>
<p>Se hai domande sul Premio Italia puoi visitare il sito <a href="https://www.premioitalia.org/">https://www.premioitalia.org/</a> o scrivere a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
<p><br></p>
<p>Distinti saluti</p>
<p>L’Organizzazione della convention</p>
@endsection