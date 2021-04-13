<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}{{ (!empty($page_title) ? ' - '.$page_title : '') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css').'?'.date('md').'B' }}" rel="stylesheet">
	<link href="/awesome/css/fontawesome.css" rel="stylesheet">
	<link href="/awesome/css/solid.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-default navbar-dark">
	<div class="container">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>	
    	</button>
		<div class="navbar-header">
			<!-- Branding Image -->
			<a class="navbar-brand premio-logo" href="/"><img src="/images/italia-250.png" class="premio-logo-trophy"><img src="/images/premio_italia_logo_t.png" id="app-navbar-collapse" alt="Premio Italia"></a>
		</div>
		<div class="collapse navbar-collapse" id="mainmenu">
			<ul class="navbar-nav">
			  <li class="nav-item">
				<a class="nav-link" href="{{ route('home') }}">Home</a>
			  </li>
			  <li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="storiaDrop" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  Storia
				</a>
				<div class="dropdown-menu" aria-labelledby="storiaDrop">
				  <a class="nav-link" href="{{ route('storia') }}">Sul Premio Italia</a>
				  <a class="nav-link" href="{{ route('albo') }}">Albo d'oro</a>
				  <a class="nav-link" href="{{ route('italcon') }}">Le Italcon</a>
				</div>
			  </li>
			  <li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="storiaDrop" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  Come funziona
				</a>
				<div class="dropdown-menu" aria-labelledby="storiaDrop">
				  <a class="nav-link" href="{{ route('chivota') }}">Chi può votare</a>
				  <a class="nav-link" href="{{ route('comecandidarsi') }}">Come ci si candida</a>
				  <a class="nav-link" href="{{ route('sistemavoto') }}">Sistema di voto</a>
				  <a class="nav-link" href="{{ route('regolamento') }}">Regolamento</a>
				</div>
			  </li>
			  <li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="storiaDrop" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  Edizione {{ $annata->anno }}
				</a>
				<div class="dropdown-menu" aria-labelledby="storiaDrop">
				  	<a class="nav-link" href="{{ route('calendario') }}">Calendario</a>
				  @if($annata->finalisti() && !$annata->risultati())
					<a class="nav-link" href="{{ route('finalisti') }}">Finalisti {{ $annata->anno }}</a>
				  @endif
				  @if($annata->risultati())
					<a class="nav-link" href="{{ route('finalisti') }}">Risultati {{ $annata->anno }}</a>
				  @endif
				</div>
			  </li>
			  @auth
			  <li class="nav-item">
				<a class="nav-link" href="{{ route('vote') }}">Vota</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="{{ route('logout') }}">Esci</a>
			  </li>
			  @endauth
			</ul>
		</div>
	</div>
</nav>

<div class="container main-containter logo-aligned">
@if(isset($_SERVER['HTTP_USER_AGENT']) && (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)))
<div class="alert alert-warning">Attenzione, sembra che tu stia usando MS Internet Explorer. Questo sito è ottimizzato per browser moderni (Chrome, Firefox, Safari, MS Edge e altri). Usando Explorer potresti vedere il sito in modo confuso e non riuscire a salvare il tuo voto.</div>
@endif

@yield('content')
</div>

<footer>
<div class="container logo-aligned">
	<div class="row">
		<div class="col-sm-12">
			Il logo del Premio Italia è stato disegnato da Eta Musciàd. L'immagine del monolito è di Franco Brambilla.<br/>
			Il sito web è stato creato da Silvio Sosio. Il codice è disponibile su <a href="https://github.com/esterisk/premioitalia">Github.com</a>.
			A cura del <a href="{{ route('comitato') }}">Comitato Organizzatore del Premio Italia</a>
			 - <a href="{{ route('privacy') }}">Trattamento dei dati personali</a>
			 - <a href="{{ route('regolamento') }}">Regolamento del Premio Italia</a>
		</div>	
	</div>
</div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
<script src="/js/app.js?{{ date('Ymd') }}C"></script>
</body>
</html>