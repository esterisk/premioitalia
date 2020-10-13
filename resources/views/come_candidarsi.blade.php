@extends('layouts.app')

@section('content')
	<div class="row">
		<h1 class="col-sm-12">{{ $page_title }}</h1>
	</div>
	<div class="row row-regolamento">
		<div class="col-sm-12">
			<p>Il Premio Italia è un premio a votazione popolare. Per partecipare, con un'opera o per l'attività che viene svolta nel campo della fantascienza o del fantastico, non è necessario inviare domande di iscrizione. L'unica cosa che occorre fare è convincere chi ha diritto di voto a segnalare la propria proposta.</p>
			<p>Non tutti possono votare (vedi <a href="{{ route('chivota') }}">chi può votare</a>) ma i votanti sono comunque molto numerosi. Per candidarsi è necessario fare in modo che chi vota venga a conoscenza della propria proposta; per esempio facendola conoscere sul web, nelle riviste di settore, sui social.</p>
			<p>Dall'edizione 2020 sarà anche possibile inserire su una bacheca su questo sito le proprie proposte.</p>
			<p>Prima di proporre un'opera o un'attività verificare sul <a href="{{ route('regolamento') }}">regolamento</a> che possa partecipare e per quale categoria va proposta. Se è un'opera, verificare in particolare che sia uscita per la prima volta nell'anno precedente e che rispetti i criteri richiesti per la lunghezza.</p>
		</div>	
	</div>
	
@endsection
