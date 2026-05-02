@extends('emails.base_text')

@section('title')
Conferma iscrizione al voto al premio Italia
@endsection

@section('main')
Caro {{ $user->firstname }},
è stata richista l’iscrizione al voto al Premio Italia a nome “{{$richiedente}}" con questo indirizzo email.
Se l’iscrizione era a tuo nome, ti confermiamo che risulti già iscritto. A votazioni aperte riceverai l’accesso via email o potrai richiederlo dalla home page del sito https://www.premioitalia.org/.
Se la richiesta non era a tuo nome, tieni presente che ogni iscritto deve essere registrato con un proprio indirizzo email. Se vuoi, fai richiesta nuovamente specificando un indirizzo email diverso.
Se hai domande sul Premio Italia puoi visitare il sito
https://www.premioitalia.org/
o scrivere a staff@premioitalia.org.

Distinti saluti
L’Organizzazione della convention
@endsection