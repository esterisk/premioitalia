<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="it">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>@yield('title')</title>
</head>

<body style="margin: 40px;font-family:'Helvetica Neue','Helvetica',sans-serif">
	<img src="{{ asset('images/premio_italia_logo_mail.png')}}" alt="Premio Italia logo">
	<div style="font-size:18px;line-height:1.3">
		<p>{{ _date('now', 'd M Y') }}</p>
		<p><br></p>

		@yield('main')

		<p style="text-align:right;margin-right:2em;">Distinti saluti<br>Organizzazione Premio Italia</p>

	</div>
	<p><br></p>
	<div style="font-size: 18px;line-height:1.3">
		<p><b>Se questa email è finita nello spam</b> per favore istruisci il tuoi sistema antispam a considerare validi questi messaggi e tutti i messaggi provenienti da @premioitalia.org. Noi promettiamo che ti invieremo solo il minimo necessario per consentirti di votare.</p>
		<p>Questa email &egrave; personale e riservata a {{ $user->name }}. Se questa email non &egrave; stata ricevuta da {{ $user->name }}, ti preghiamo di ignorare quanto sopra e farla pervenire al corretto destinatario, o segnalare il problema a <a
				href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
		<p>Se hai domande sul Premio Italia puoi visitare il sito <a href="https://www.premioitalia.org/">https://www.premioitalia.org/</a> o scrivere a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
	</div>
	@if(isset($user->token))
	<div style="font-size: 16px;line-height:1.3">
		<p>Se non vuoi più ricevere comunicazioni dal Premio Italia puoi chiedere l'esclusione dal registro degli elettori cliccando qui sotto:<br>
			<a href="{{ route('unsubscribe', [ 'id' => $user->id, 'token' => $user->token]) }}" style="font-weight: bold">Rimuovimi dai votanti dal Premio Italia - Unsubscribe</a>
	</div>
	@endif
	<p><br></p>

</body>

</html>