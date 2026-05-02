@extends('emails.base_text')

@section('title')
Ricordati di inviare il voto!
@endsection

@section('main')
Caro {{ $user->name }},
Stamattina è partita di nuovo, per errore, la mail di avviso di cambio di finalisti che avevamo inviato tempo fa. 
Ci scusiamo, è stato uno sbaglio: non ci sono stati altri cambiamenti.
Grazie, a presto!
@endsection