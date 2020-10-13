@extends('emails.base')

@section('title')
Ricordati di inviare il voto!
@endsection

@section('main')
<p>Caro {{ $user->name }},</p>
<p>abbiamo notato che hai inserito il voto per il Premio Italia ma l'<b>hai solo salvato senza inviarlo</b>.</p>
<p>Tra poco (il voto è aperto fino alla mezzanotte del {{ Cutter::date(($annata->fase() == 'fase1' ? $annata->fase_1_a : $annata->fase_2_a), '%e %B') }}) scadrà il termine per votare; ricorda che se non avrai inviato il voto, esso non potrà essere preso in considerazione.</p>
<p>Per inviare il voto, entra nel sistema di voto cliccando su questo pulsante:</p>
<p><br></p>
<p><a href="https://www.premioitalia.org/entra/{{ urlencode($user->id) }}/{{ urlencode($user->token) }}" style="text-decoration:none;padding:15px;border-radius:4px;border:1px solid #06f;background:#06f;color:white;font-size:18px">Voto Premio Italia</a></p>
<p><br></p>
<p>Conferma la tua identità all'inizio della pagina, poi vai in fondo e clicca il pulsante «Invia il voto».</p>
<p>Grazie!</p>
@endsection