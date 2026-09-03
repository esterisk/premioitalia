<?php

namespace App\Http\Controllers;

use App\Enums\StatoApplicationEnum;
use App\Models\Application;
use App\Models\Convention;
use App\Models\User;
use App\Services\ExifPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Cartella, sul disco privato, in cui vengono conservati i selfie di verifica.
     */
    private const CARTELLA_SELFIE = 'application_selfies';

    /**
     * Età massima, in minuti, dello scatto di un selfie perché i suoi dati EXIF
     * possano confermare la posizione.
     */
    private const ETA_MASSIMA_SELFIE = 60;

    public function show(Request $request): View
    {
        $convention = Convention::inCorso()->first();

        return view('applicazione', [
            'page_title' => 'Richiesta di iscrizione',
            'convention' => $convention,
            'raggio' => Convention::RAGGIO_APPLICAZIONE,
            'selfieSempreRichiesto' => $convention ? $this->selfieSempreRichiesto($request, $convention) : false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $convention = Convention::inCorso()->first();

        if (! $convention) {
            return redirect()->route('applicazione');
        }

        $dati = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cognome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'selfie' => ['nullable', 'image', 'max:8192'],
        ], [
            'nome.required' => 'Indica il tuo nome.',
            'cognome.required' => 'Indica il tuo cognome.',
            'email.required' => 'Indica il tuo indirizzo email.',
            'email.email' => 'L’indirizzo email non sembra valido.',
            'selfie.image' => 'Il file caricato non è un’immagine.',
            'selfie.max' => 'L’immagine è troppo grande: il limite è 8 MB.',
        ]);

        $latitudine = isset($dati['latitude']) ? (float) $dati['latitude'] : null;
        $longitudine = isset($dati['longitude']) ? (float) $dati['longitude'] : null;

        $posizione = $latitudine !== null && $longitudine !== null
            ? ['lat' => $latitudine, 'lng' => $longitudine, 'source' => 'browser']
            : null;
        $confermata = $convention->coordinateVicine($latitudine, $longitudine);

        if ((! $confermata || $this->selfieSempreRichiesto($request, $convention)) && ! $request->hasFile('selfie')) {
            return back()
                ->withInput()
                ->withErrors(['selfie' => 'Non è stato possibile confermare che ti trovi alla convention: carica una foto che ritragga te e il cartello del Premio Italia.']);
        }

        if ($errore = $this->erroreUtenteEsistente($dati)) {
            return back()->withInput()->withErrors(['email' => $errore]);
        }

        $snapshot = null;

        if ($selfie = $request->file('selfie')) {
            // Senza posizione dal browser la conferma può arrivare dai dati EXIF del
            // selfie, purché lo scatto sia recente e avvenuto vicino alla convention.
            $exif = ExifPosition::fromFile($selfie->getRealPath());

            if ($exif->hasCoordinate()) {
                $posizioneExif = [
                    'lat' => $exif->latitudine,
                    'lng' => $exif->longitudine,
                    'source' => 'exif',
                    'taken_at' => $exif->scattataIl?->toDateTimeString(),
                ];

                if (! $confermata
                    && $exif->recente(self::ETA_MASSIMA_SELFIE)
                    && $convention->coordinateVicine($exif->latitudine, $exif->longitudine)) {
                    $confermata = true;
                    $posizione = $posizioneExif;
                } else {
                    $posizione ??= $posizioneExif;
                }
            }

            $snapshot = $selfie->store(self::CARTELLA_SELFIE, 'local');
        }

        $omonimo = User::findByName($dati['nome'], $dati['cognome']);

        $application = new Application([
            'convention_id' => $convention->id,
            'first_name' => $dati['nome'],
            'last_name' => $dati['cognome'],
            'email' => $dati['email'],
            'user_id' => $omonimo?->id,
            'location' => $posizione,
            'location_confirmed' => $confermata,
            'snapshot' => $snapshot,
            'ip_address' => (string) $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'status' => $omonimo ? StatoApplicationEnum::DaVerificare : StatoApplicationEnum::InAttesa,
            'notes' => $omonimo
                ? 'Esiste già un elettore con lo stesso nome e cognome ma con un indirizzo email diverso.'
                : null,
        ]);
        $application->save();

        $application->sendConfirmation();

        return redirect()->route('applicazione')->with('applicazione_inviata', true);
    }

    /**
     * Il selfie è comunque richiesto se la convention non ha una location con cui
     * confrontarsi o se dallo stesso dispositivo è già arrivata un'altra richiesta.
     */
    private function selfieSempreRichiesto(Request $request, Convention $convention): bool
    {
        return ! $convention->hasLocation()
            || Application::dispositivoGiaUsato((string) $request->ip(), (string) $request->userAgent());
    }

    /**
     * Verifica se l'email è già in uso da un elettore registrato.
     *
     * @param  array{nome: string, cognome: string, email: string}  $dati
     */
    private function erroreUtenteEsistente(array $dati): ?string
    {
        $utente = User::findByEmail($dati['email']);

        if (! $utente) {
            return null;
        }

        if ($utente->hasName($dati['nome'], $dati['cognome'])) {
            return 'Risulti già registrato fra gli elettori del Premio Italia: non serve una nuova richiesta. A votazioni aperte riceverai l’accesso via email.';
        }

        return 'Questo indirizzo email è già associato a un altro elettore. Ogni elettore deve avere un proprio indirizzo email: fai la richiesta con un indirizzo diverso.';
    }
}
