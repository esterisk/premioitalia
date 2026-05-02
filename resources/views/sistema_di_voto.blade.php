@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">Il sistema di voto del Premio Italia</h1>
	</div>
	<div class="row row-regolamento">
		<div class="col-sm-12">
			<p>Il sistema di voto del Premio Italia si svolge in due fasi. Nella prima vengono segnalate le candidature; nella seconda vengono votati i finalisti.</p>
			<h2>Prima fase: segnalazione delle candidature</h2>
			<p>Capita spesso che qualcuno chieda: come faccio a partecipare con la mia opera al Premio Italia? La risposta è che non ci si può iscrivere per partecipare al Premio; sono i votanti che decidono chi partecipa, nella prima fase del Premio, quella dedicata alle segnalazioni.</p>
			<p>Ogni votante può esprimere fino a un massimo di tre candidature per ogni categoria. La scelta dei candidati è a totale discrezione del votante: è quindi importante, per chi vuole partecipare al Premio, far conoscere la propria produzione, magari fare un po' di "campagna elettorale", magari anche indicando per quale tra le proprie opere si preferirebbe ricevere segnalazioni, se si sono prodotte diverse opere papabili.</p>
			<p>Al termine della prima fase, in quel periodo di tempo chiamato "Seconda fase" i gestori del premio verificano che le candidature siano in regola con il regolamento, quindi contano quanti voti hanno ricevuto e indicano i finalisti.</p>
			<h2>La fase finale</h2>
			<p>Per la fase finale della votazione, il Premio Italia, come il Premio Hugo, ha utilizzato per anni il sistema chiamato Australian Ballot. Alcuni anni fa il sistema, considerato troppo complicato e poco comprensibile, venne abolito e sostituito dal voto secco di un singolo candidato.</p>
			<p>Nel 2018, con effetto dal 2019, è stato approvato un sistema di voto basato su una versione semplificata e più pratica dell'Australian Ballot. Che quindi ne recupera gli aspetti utili senza essere complicato per il votante.</p>
			<p>Il sistema consente al votante di esprimere, oltre al candidato preferito, anche una seconda e una terza scelta. Non obbligatoriamente: può votare un solo candidato, se prefesce. Oppure due, oppure tre.</p>
			<p>Non si tratta però di una classifica, o di punti assegnati in stile Formula Uno. Per questo non parliamo di primo, secondo e terzo ma di prima scelta, seconda scelta e terza scelta. Il voto che il votante assegna è sempre soltanto uno. Ma, nel caso la prima scelta risultasse fuori dai giochi perché poco votata, il voto verrebbe spostato sulla seconda scelta, e allo stesso modo eventualmente sulla terza scelta.</p>
			<p>È una cosa simile a quando si vota per il sindaco: votate un candidato, ma non arriva nei primi due; nella seconda tornata, il ballottaggio, votate per uno dei due candidati in finale, perché anche se non è il vostro preferito vi piace comunque più dell’altro candidato. Questo sistema di voto elimina il costo delle elezioni ripetute; è come se facesse la domanda «ok, hai votato per questo candidato, perfetto. Ma nel caso lui non fosse tra i primi, dimmi subito quale degli altri preferiresti che venisse eletto, così in caso so già che ne pensi».</p>
			<p>Questo metodo viene applicato, chiaramente, solo alla seconda fase, quella in cui si vota tra i candidati arrivati in finale.</p>
			<h2>Esempio di conteggio dei voti</h2>
			<p>Supponiamo ci siano cinque candidati, che chiameremo Asimov, Ballard, Clarke, Delany e Ellison.</p>
			<p>Supponiamo di aver votato Asimov come prima scelta, Ballard come seconda, Clarke come terza.</p>
			<p>Al primo passaggio i voti vengono assegnati ai candidati indicati nelle schede come prima scelta. Il risultato del conteggio dice che Delany ha ricevuto 7 voti, Ballard ne ha ricevuti 6, Asimov 4, Clarke ed Ellison nessuno. Stiamo contando, ripetiamo, i voti indicati come primo scelta; noi avevamo votato Clarke, ma come terza scelta, quindi qui non viene contato. Il nostro voto è assegnato per ora ad Asimov.</p>
			<p>Clarke ed Ellison, con zero voti, sono gli ultimi classificati; vengono eliminati.</p>
			<p>A questo punto l’ultimo in classifica è Asimov. Anche Asimov viene eliminato. Asimov ha ricevuto quattro voti, tra i quali il nostro: questi quattro voti possono essere ridistribuiti. Poiché nella nostra scheda abbiamo indicato come seconda scelta Ballard, il nostro voto viene trasferito a Ballard. Anche un altro elettore ha indicato Ballard come seconda scelta. Il terzo elettore che aveva votato Asimov ha indicato Clarke come seconda scelta, ma Clarke è stato già eliminato; come terza scelta ha indicato Ballard; anche questo voto quindi va a Ballard. Il quarto elettore ha indicato Ellison come seconda scelta e Delany come terza; il voto va quindi a Delany, perché anche Ellison è già eliminato.</p>
			<p>Dopo questi passaggi Ballard ha tre voti di più, andando a quota 9; Delany ha un voto in più e va a quota 8. Ballard quindi è eletto vincitore.</p>
			<h2>Come si vota in pratica</h2>
			<p>Il modo per indicare le proprie scelte è più semplice da usare che da spiegare.</p>
			<p>Cliccando in successione i candidati scelti, si indicano prima, seconda e poi terza scelta.</p>
			<p>Se ho cambiato idea e voglio come seconda scelta il candidato che ho indicato come terza scelta, devo cliccare di nuovo su di esso: passarà secondo, mentre il secondo passerà terzo. Cliccando ancora passerà primo, mentre il secondo passerà terzo.</p>
			<p>Se non voglio più un candidato che ho selezionato, devo cliccare su di esso finché non diventa prima scelta; a questo punto, ancora un clic e il candidato viene tolto.</p>
			<p>Se voglio rifare tutto e pulire le scelte fatte mi basta cliccare sul pulsante "Non voto per questa categoria".</p>
			<p> </p>
			<p>Chi è interessato a discutere di questi argomenti, o vuole chiedere informazioni, può iscriversi al gruppo Facebook "Amici del premio Italia", aperto a tutti.</p>
			</div>	
	</div>
	
@endsection
