@extends('emails.base_text')

@section('title')
Ricordati di inviare il voto!
@endsection

@section('main')
Caro {{ $user->name }},
abbiamo notato che hai inserito il voto per il Premio Italia ma l'hai solo salvato senza inviarlo.
Tra poco (il voto è aperto fino alla mezzanotte del {{ Cutter::date(($annata->fase() == 'fase1' ? $annata->fase_1_a : $annata->fase_2_a), '%e %B') }}) scadrà il termine per votare; ricorda che se non avrai inviato il voto, esso non potrà essere preso in considerazione.
Per inviare il voto, entra nel sistema di voto usa incolla l'indirizzo qui sotto nel tuo browser:

https://www.premioitalia.org/entra/{{ urlencode($user->id) }}/{{ urlencode($user->token) }}

Conferma la tua identità all'inizio della pagina, poi vai in fondo e clicca il pulsante «Invia il voto».
Grazie!
@endsection