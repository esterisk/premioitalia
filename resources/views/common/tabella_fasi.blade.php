<table cellpadding="3" cellspacing="0" class="table">
<tr>
<th>Fase 1: segnalazioni</th>
<td>Votazione aperta da {{ Cutter::date($annata->fase_1_da, 'd M') }} a {{ Cutter::date($annata->fase_1_a, 'd M Y') }}</td>
</tr>
<tr>
<th>Fase 2: valutazione</th>
<td>Spoglio da {{ Cutter::date(Cutter::dateAdd($annata->fase_1_a, 1), 'd M') }} a {{ Cutter::date($annata->fase_2_da, 'd M Y') }}</td>
</tr>
<tr>
<th>Fase 3: votazione finalisti</th>
<td>Votazione aperta da {{ Cutter::date($annata->fase_2_da, 'd M') }} a {{ Cutter::date($annata->fase_2_a, 'd M Y') }}</td>
</tr>
<tr>
<th>Fase 4: valutazione</th>
<td>Spoglio da {{ Cutter::date(Cutter::dateAdd($annata->fase_2_a, 1), 'd M') }} (votazioni chiuse)</td>
</tr>
<tr>
<th>Annuncio risultati e premiazioni</th>
<td>La notte di {{ Cutter::date($annata->premiazione, 'd M Y') }}</td>
</tr>
</table>
