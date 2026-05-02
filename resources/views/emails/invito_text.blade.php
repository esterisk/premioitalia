@extends('emails.base_text')

@section('title')
Invito al voto al premio Italia
@endsection

@section('main')
Caro {{ $user->firstname }},
ricevi questa email in quanto iscritto all'edizione {{$convention->titolo_edizione}}.
In quanto membro iscritto alla manifestazione, che è stata sede Italcon
o è affiliata all'Italcon, hai diritto a votare per il Premio Italia,
il premio nazionale per le opere e le attività nel settore del fantastico
e della fantascienza conferito dall'Italcon, il convegno nazionale del fantastico.
Se hai già votato negli ultimi anni e quindi sei già registrato nel sistema
di voto non devi fare nulla. Se non hai ancora votato e desideri farlo,
puoi iscriverti al sistema di voto andando a questo indirizzo:

{{ $register_route }}

Sulla pagina, se non li troverai precompilati, dovrai inserire questi dati:

Nome: {{ $user->firstname }}
Cognome: {{ $user->lastname }}
Email: {{ $user->email }}

Se questo indirizzo email non corrisponde al nome e cognome,
contatta staff@premioitalia.org.
Se non hai partecipato alla convention di cui sopra e ritieni
questa email un errore, non fare nulla o contatta l'organizzazione
della convention per fare rettificare i dati.
Se hai domande sul Premio Italia puoi visitare il sito
https://www.premioitalia.org/
o scrivere a staff@premioitalia.org

Distinti saluti
L’Organizzazione della convention
@endsection