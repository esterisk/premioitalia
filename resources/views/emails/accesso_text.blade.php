@extends('emails.base_text')

@section('title')
Invito al voto
@endsection

@section('main')
Caro {{ $user->name }},
ricevi questa email in quanto hai richiesto di poter votare per il Premio Italia e ne hai facoltà in quanto elettore registrato.
Ti ricordiamo che la prima fase del voto sarà aperta dal {{ Cutter::date($annata->fase_1_da, 'd M') }} al {{ Cutter::date($annata->fase_1_a, 'd M') }}.
La seconda fase del voto sarà aperta dal {{ Cutter::date($annata->fase_2_da, 'd M') }} al {{ Cutter::date($annata->fase_2_a, 'd M') }}.

Puoi entrare nel sistema di voto andando col browser web a questo indirizzo

https://www.premioitalia.org/entra/{{ urlencode($user->id) }}/{{ urlencode($user->token) }}

@endsection