@php $fase = $annata->fase(); @endphp
<table cellpadding="3" cellspacing="0" class="table tabella-fasi">
    <tr {!! $fase == 'fase0' ? 'class="active"' : '' !!}>
        <th><strong>Fase 0: candidature spontanee</strong>
            <br/><small>Fase aperta a tutti, per la segnalazione di candidati che verranno inclusi in una long list non esclusiva proposta ai votanti nella fase successiva.</small<br />
        </th>
        <td>Votazione aperta da {{ _date($annata->candidature_da, 'd M') }} a {{ _date($annata->candidature_a, 'd M Y') }}</td>
    </tr>
    <tr {!! $fase == 'fase1' ? 'class="active"' : '' !!}>
        <th>
            <strong>Fase 1: segnalazioni</strong><br/>
            <small>Gli elettori registrati possono segnalare cinque candidati per categoria, scegliendoli dalla long-list delle candidature spontanee o inserendoli ex novo, dai quali verranno selezionati i finalisti.</small>
        </th>
        <td>Votazione aperta da {{ _date($annata->fase_1_da, 'd M') }} a {{ _date($annata->fase_1_a, 'd M Y') }}</td>
    </tr>
    <tr {!! $fase == 'spoglio1' ? 'class="active"' : '' !!}>
        <th>
            <strong>Spoglio 1: valutazione</strong><br/>
            <small>I gestori del premio verificano la validità dei candidati ed eseguono i conteggi per la selezione dei candidati.</small>
        </th>
        <td>Spoglio da {{ _date(_dateAdd($annata->fase_1_a, 1), 'd M') }} a {{ _date($annata->fase_2_da, 'd M Y') }}</td>
    </tr>
    <tr {!! $fase == 'fase2' ? 'class="active"' : '' !!}>
        <th>
            <strong>Fase 2: votazione finalisti</strong><br/>
            <small>Gli elettori registrati votano le loro preferenze tra i candidati finalisti.</small>
        </th>
        <td>Votazione aperta da {{ _date($annata->fase_2_da, 'd M') }} a {{ _date($annata->fase_2_a, 'd M Y') }}</td>
    </tr>
    <tr {!! $fase == 'spoglio2' ? 'class="active"' : '' !!}>
        <th>
            <strong>Spoglio 2: valutazione</strong><br/>
            <small>I gestori del premio eseguono i conteggi per la definizione dei vincitori.</small>
        </th>
        <td>Spoglio da {{ _date(_dateAdd($annata->fase_2_a, 1), 'd M') }} (votazioni chiuse)</td>
    </tr>
    <tr>
        <th><strong>Annuncio risultati e premiazioni</strong></th>
        <td>La notte di {{ _date($annata->premiazione, 'd M Y') }}</td>
    </tr>
</table>
