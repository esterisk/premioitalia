@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">{{ $page_title }}</h1>
	</div>
	<div class="row row-regolamento">
		<div class="col-sm-12">
			<p>Il Premio Italia rappresenta la celebrazione dell'apprezzamento dalla comunità degli appassionati di fantascienza e fantastico italiani per la produzione italiana dell'anno precedente. Il premio è assegnato durante <b>Italcon</b>, il convegno nazionale del settore; è la comunità dei partecipanti dell’Italcon e alla loro assemblea che assegna il premio e ne determina il regolamento.</p>
			<h2>Gli inizi</h2>
			<p>Il Premio Italia è stato istituito nel <b>1972</b>, in occasione della prima grande convention italiana, l’<b>Eurocon di Trieste</b>. In quella occasione venne assegnato per la prima volta il Premio Europa (oggi chiamato ESFS Award) con alcune categorie dedicate alla produzione italiana; questa premiazione viene considerata la prima edizione del Premio Italia, anche se questo nome sarebbe arrivato solo dopo diversi anni.</p>
			<p>Il premio era strutturato sul modello del <b>Premio Hugo</b> assegnato nelle Worldcon, con diverse categorie – romanzo, racconto, artista, eccetera. Negli anni le categorie sono cambiate, nuove ne sono state aggiunte, passando dalle nove originali alle venti attuali.</p>
			<p>Dal 1975 al 1978 il premio venne assegnato nel corso degli <b>S.F.I.R.</b> ("Science Fiction Italian Roundabout") tenuti a Ferrara. Nel <b>1980 a Stresa</b>, la seconda Eurocon tenuta in Italia, venne istituito il nome “Italcon” e redatte le prime versioni dei regolamenti di Italcon e Premio Italia.</p>
			<h2>La gestione del premio</h2>
			<p>Organizzato ogni anno, anche con formule leggermente diverse, dagli organizzatori delle varie Italcon che si sono succedute, è ancora una volta in occasione di un’Eurocon, quella del <b>1989 a San Marino</b>, che avviene una svolta importante: l’oganizzazione del premio viene affidata an ente di controllo esterno, la <b>World SF Italia</b>, per assicurarne la continuità e la regolarità.</p>
			<p>World SF Italia è la sezione italiana di un’iniziativa internazionale, fondata da Harry Harrison e Frederik Pohl, istituita per promuovere la fantascienza non anglofona. In Italia l'associazione assume la funzione di una federazione dei professionisti del settore, e come tale continuerà a esistere anche molti anni dopo l’estinzione della sua controparte internazionale. È <b>Ernesto Vegetti</b>, che in seguito sarà nominato presidente dell’associazione, a occuparsi in prima persona della gestione del premio garantendone il corretto svolgimento, fino alla sua scomparsa nel gennaio 2010. Vegetti formalizza il regolamento e la sua applicazione e modalità di modifica, gestisce lo spoglio dei voti, trasforma, in poche parole, quella che era una vaga idea soggetta a interpretazioni personali degli organizzatori delle Italcon in un'istituzione chiara, regolare e ben definita.</p>
			 <p>Già dalla metà degli anni Novanta alla gestione del premio aveva cominciato a collaborare Silvio Sosio, che contribuirà al passaggio al voto elettronico, nei primi anni Duemila, sviluppandone la struttura informatica e la prima versione del <a href="https://www.premioitalia.org">sito web</a>. </p>
			<p>Dopo la scomparsa di Vegetti è Sosio, a sua volta socio della World SF, a occuparsi della gestione del premio. Dopo l’edizione del 2016 la World SF decide di chiudere i propri rapporti con l’Italcon e ritirarsi dalla gestione del premio; viene quindi costituito un Comitato di gestione del premio, presieduto da Silvio Sosio, che a oggi continua a garantire, su mandato dell’assemblea dell’Italcon, il corretto funzionamento del premio.</p>
			<p>Nel 2017 su iniziativa di Silvio Sosio è stato redatto e approvato un <a href="{{ route('regolamento') }}">nuovo regolamento</a>, che armonizzasse e riordinasse tutte le modifiche intervenute nel corso degli anni.</p>
			<h2>L'estensione dei votanti</h2>
			<p>Il più grave problema che ha dovuto affrontare il Premio Italia, negli ultimi decenni, è stata la diminuzione del numero dei votanti. Se negli anni settanta e ottanta alle convention di fantascienza partecipavano molte centinaia di persone, il progressivo calo dei votanti, rimasto per vari anni anche sotto quota cento, aveva reso il premio facilmente orientabile da gruppi organizzati. Per risolvere questo problema, dal 2013 sono state prese una serie di misure che hanno esteso il voto ai partecipanti di tutte le Italcon precedenti e anche ai partecipanti di altre convention, definite “affiliate al Premio Italia”. Questo ha contribuito a far ritrovare al premio una sua attendibilità, raggiungendo e superando i quattrocento votanti nella fase finale già dall’edizione del 2017. In una pagina di questo sito ci sono <a href="{{ route('chivota') }}">maggiori informazioni sul diritto di voto</a>.
			<h2>Il logo e il trofeo</h2>
			<p>Nel 1999 è stato disegnato dall’artista <b>Eta Musciàd</b> quello che è tuttora il logo del premio, che unisce il simbolo del sole e l’astronave (stemma dell’Impero Galattico nella saga della Fondazione di Asimov) con una spada e alcuni gargoyle, a rappresentare i vari generi del fantastico: fantascienza, fantasy, horror.</p>
			<p>Negli anni il Premio Italia è stato assegnato fisicamente in una varietà di forme; diplomi, targhe, targhette, trofei in plastica e in vetro. Nel 2018 si è tentato di definire una forma specifica che possa diventare caratterizzante: un monolito di plexiglass, che rispetta le dimensioni del monolito di <i>2001: Odissea nello spazio</i> (1 x 4 x 9, i quadrati dei primi tre numeri primi).</p>
			<p>Dal 2017 esiste su Facebook il gruppo <a href="https://www.facebook.com/groups/premioitalia/">Amici del Premio Italia</a> per chi voglia saperne di più, discutere sul premio, sul suo funzionamento e sul suo futuro.</p>
		</div>	
	</div>
	
@endsection