@extends('emails.base')

@section('title')
Invito al voto al premio Italia
@endsection

@section('main')
<p>Caro {{ $user->firstname }},</p>
<p>ricevi questa email in quanto iscritto all&rsquo;edizione {{$convention->titolo_edizione}}.</p>
<p>In quanto membro iscritto alla manifestazione, che &egrave; stata sede Italcon o &egrave; affiliata all&rsquo;Italcon, hai diritto a votare per il Premio Italia, il premio nazionale per le opere e le attivit&agrave; nel settore del fantastico e della fantascienza conferito dall&rsquo;Italcon, il
    convegno nazionale del fantastico.</p>
<p>Se hai gi&agrave; votato negli ultimi anni e quindi sei gi&agrave; registrato nel sistema di voto non devi fare nulla. Se non hai ancora votato e desideri farlo, puoi iscriverti al sistema di voto cliccando su questo pulsante:</p>
<p><br></p>
<p><a href="{{ $register_route }}" style="text-decoration:none;padding:15px;border-radius:4px;border:1px solid #039;background:#039;color:white;font-size:18px">Premio Italia ammissione elettori</a></p>
<p><br></p>
<p>Sulla pagina, troverai precompilati questi dati:</p>
<p><br></p>
<p>Nome: {{ $user->firstname }}</p>
<p>Cognome: {{ $user->lastname }}</p>
<p>Email: {{ $user->email }}</p>
<p><br></p>
<p>Se i dati sono corretti, premi il pulsante "Conferma l'iscrizione" per essere inserito tra gli elettori del Premio Italia.</p>
<p>Se i dati non sono corretti contatta <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
<p>Se non hai partecipato alla convention di cui sopra e ritieni questa email un errore, non fare nulla o contatta l’organizzazione della convention per fare rettificare i dati.</p>
<p>Se hai domande sul Premio Italia puoi visitare il sito <a href="https://www.premioitalia.org/">https://www.premioitalia.org/</a> o scrivere a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
<p><br></p>
<p>Distinti saluti</p>
<p>L’Organizzazione della convention</p>
@endsection