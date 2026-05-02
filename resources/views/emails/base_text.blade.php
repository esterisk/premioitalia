{{ _date('now', 'd M Y') }}

@yield('main')

Distinti saluti
Organizzazione Premio Italia

Se questa email è finita nello spam per favore istruisci il tuoi sistema antispam a considerare validi questi messaggi e tutti i messaggi provenienti da @premioitalia.org. Noi promettiamo che ti invieremo solo il minimo necessario per consentirti di votare.

Questa email è personale e riservata a {{ $user->name }}. Se questa email non è stata ricevuta da {{ $user->name }}, ti preghiamo di ignorare quanto sopra e farla pervenire al corretto destinatario, o segnalare il problema a staff@premioitalia.org.
Se hai domande sul Premio Italia puoi visitare il sito https://www.premioitalia.org/ o scrivere a staff@premioitalia.org.

@if(isset($user->token))
-------------------------------
Se non vuoi più ricevere comunicazioni dal Premio Italia puoi chiedere l'esclusione dal registro degli elettori andando all'indirizzo qui sotto:
Unsubscribe: {{ route('unsubscribe', [ 'id' => $user->id, 'token' => $user->token]) }}
-------------------------------
@endif