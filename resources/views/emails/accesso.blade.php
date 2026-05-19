@extends('emails.base')

@section('title')
Accesso al voto al Premio Italia {{ $annata->anno }}
@endsection

@section('main')
<p>Caro {{ $user->name }},</p>
<p>ricevi questa email in quanto hai richiesto di poter votare per il Premio Italia e ne hai facolt&agrave; in quanto elettore registrato.</p>
@if($annata->fase() == 'fase1')
<p>È aperta la <b>prima fase delle votazioni: la segnalazione</b> di candidati relativi al periodo di riferimento del {{ $annata->anno - 1 }}. Il voto è aperto fino alla mezzanotte {{ _date($annata->fase_1_a, 'j F') }}.</p>
@elseif($annata->fase() == 'fase2')
<p>È aperta la <b>seconda fase delle votazioni: la votazione finale</b> dei candidati finalisti. Il voto è aperto fino alla mezzanotte {{ _date($annata->fase_2_a, 'j F') }}.</p>
@else
<p>Al momento non sono aperte votazioni. Conserva se vuoi questa email per accedere al voto quando sarà aperto.</p>
<p>Ti ricordiamo che la prima fase del voto sarà aperta dal {{ _date($annata->fase_1_da, 'd M') }} al {{ _date($annata->fase_1_a, 'd M') }}.</p>
<p>La seconda fase del voto sarà aperta dal {{ _date($annata->fase_2_da, 'd M') }} al {{ _date($annata->fase_2_a, 'd M') }}.</p>
@endif
<p>Puoi entrare nel sistema di voto cliccando su questo pulsante:</p>
<p><br></p>
<p><a href="{{ route('login-by-email', [ 'id' => $user->id, 'token' => $user->token]) }}" style="text-decoration:none;padding:15px;border-radius:4px;border:1px solid #039;background:#039;color:white;font-size:18px">Voto Premio Italia</a></p>
<p><br></p>
<p>Oppure inserisci nel tuo browser preferito l'indirizzo:</p>
<p>{{ route('login-by-email', [ 'id' => $user->id, 'token' => $user->token]) }}</p>
<p><br></p>
<p>Ti raccomandiamo di usare un browser recente (Chrome, Safari, Firefox v. 4 o successive, Edge) per evitare problemi di compatibilità.</p>
<p><b>ATTENZIONE:</b> questa email ti garantisce l'accesso al sito senza password, NON INOLTRARLA AD ALTRE PERSONE.</p>
@if ($sollecito)
<p><br></p>
<p>Questo è la seconda mail di invito che ricevi, nel caso avessi dimenticato o non ricevuto la prima.</p>
<p>Non ne invieremo altre per questa fase del voto.</p>
@endif

<img src="{{ route('pixel', [ 'u' => $user->id, 'm' => urlencode($annata->anno.' '.$annata->fase().($sollecito ? ' R' : '' ))]) }}" alt="">
@endsection