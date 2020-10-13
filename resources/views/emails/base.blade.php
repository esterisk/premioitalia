<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html lang="it">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>@yield('title')</title>
</head>
<body style="margin: 40px;font-family:'Helvetica Neue','Helvetica',sans-serif">
<div style="font-size:18px;line-height:1.3">
<p>{{ Cutter::date('now', 'd M Y') }}</p>
<p><br></p>
<p><br></p>

@yield('main')

<p><br></p>
<p style="text-align:right;margin-right:2em;">Distinti saluti</p>
<p style="text-align:right;margin-right:2em;">Organizzazione Premio Italia</p>

</div>
<p><br></p>
<div style="font-size: 18px;line-height:1.3">
<p>Questa email &egrave; personale e riservata a {{ $user->name }}. Se questa email non &egrave; stata ricevuta da {{ $user->name }}, ti preghiamo di ignorare quanto sopra e farla pervenire al corretto destinatario, o segnalare il problema a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
<p>Se hai domande sul Premio Italia puoi visitare il sito <a href="https://www.premioitalia.org/">https://www.premioitalia.org/</a> o scrivere a <a href="mailto:staff@premioitalia.org">staff@premioitalia.org</a>.</p>
</div>
<div style="font-size: 12px;line-height:1.3">
<p>Se non vuoi più ricevere comunicazioni dal Premio Italia puoi chiedere l'esclusione dal registro degli elettori cliccando qui sotto:</p>
<p><a href="https://www.premioitalia.org/unsubscribe/{{ urlencode($user->id) }}/{{ urlencode($user->token) }}">Rimuovimi dai votanti dal Premio Italia - Unsubscribe</a>
</div>
<p><br></p>

</body>
</html>