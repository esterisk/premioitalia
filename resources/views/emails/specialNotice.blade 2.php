@extends('emails.base')

@section('title')
Problemi server Premio Italia
@endsection

@section('main')
<p>Caro {{ $user->name }},</p>
<p>Se hai votato al premio Italia venerdì 7 ottobre dobbiamo chiederti di votare di nuovo.</p>
<p>Alla mattina di sabato 8 ottobre il nostro server ha subito gravi danni, i dischi sono saltati con la perdita dei dati.</p>
<p>La situazione è stata ripristinata completamente solo la sera del lunedì 10 ottobre, con i dati che erano stati salvati nel backup giornaliero.</p>
<p>Tuttavia, questo backup viene eseguito alla mattina presto, e l'ultimo che abbiamo avuto a disposizione prima che il server saltasse era stato salvato venerdì mattina. Quindi i dati inseriti nella giornata di venerdì sono andati perduti.</p>
<p>Se hai votato venerdì il voto è andato perso. Lo stiamo comunicando a tutti coloro che ci risultano non aver ancora votato, perciò <b>se ricevi questa mail certamente il tuo voto non risulta nel nostro archivio</b>.</p>
<p>Se non avevi votato scusa l'intrusione. Ne approfittiamo per incoraggiarti a votare ora :-)</p>
<p>Per inviare il voto, entra nel sistema di voto cliccando su questo pulsante:</p>
<p><br></p>
<p><a href="https://www.premioitalia.org/entra/{{ urlencode($user->id) }}/{{ urlencode($user->token) }}" style="text-decoration:none;padding:15px;border-radius:4px;border:1px solid #039;background:#039;color:white;font-size:18px">Voto Premio Italia</a></p>
<p><br></p>
<p>Conferma la tua identità all'inizio della pagina, poi vai in fondo e clicca il pulsante «Invia il voto».</p>
<p>Ci scusiamo per il contrattempo – e non è neanche il primo quest'anno.</p>
<p>Grazie, a presto!</p>
@endsection