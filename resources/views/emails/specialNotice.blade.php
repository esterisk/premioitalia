@extends('emails.base')

@section('title')
Mail errata
@endsection

@section('main')
<p>Caro {{ $user->name }},</p>
<p>Stamattina è partita di nuovo, per errore, la mail di avviso di cambio di finalisti che avevamo inviato tempo fa. Ci scusiamo, è stato uno sbaglio, non ci sono stati altri cambiamenti.</p>

<p>Grazie, a presto!</p>
@endsection