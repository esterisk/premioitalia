<?php

use App\Enums\StatoApplicationEnum;
use App\Models\Application;
use App\Models\Convention;
use App\Models\User;
use App\Services\ExifPosition;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    /**
     * Cartella, sul disco privato, in cui vengono conservati i selfie di verifica.
     */
    private const CARTELLA_SELFIE = 'application_selfies';

    /**
     * Età massima, in minuti, dello scatto di un selfie perché i suoi dati EXIF
     * possano confermare la posizione.
     */
    private const ETA_MASSIMA_SELFIE = 60;

    #[Locked]
    public Convention $convention;

    public string $nome = '';

    public string $cognome = '';

    public string $email = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $selfie = null;

    /**
     * Indica se il campo per il selfie è visibile: lo decide il browser dopo aver
     * rilevato la posizione, oppure il server quando la conferma non riesce.
     */
    public bool $selfieRichiesto = false;

    #[Locked]
    public bool $selfieSempreRichiesto = false;

    #[Locked]
    public bool $inviata = false;

    public function mount(Convention $convention): void
    {
        $this->convention = $convention;
        $this->selfieSempreRichiesto = $this->selfieSempreRichiesto();
        $this->selfieRichiesto = ! $convention->hasLocation();
    }

    public function invia(): void
    {
        $dati = $this->validate([
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

        $posizione = $this->latitude !== null && $this->longitude !== null
            ? ['lat' => $this->latitude, 'lng' => $this->longitude, 'source' => 'browser']
            : null;
        $confermata = $this->convention->coordinateVicine($this->latitude, $this->longitude);

        if ((! $confermata || $this->selfieSempreRichiesto()) && ! $this->selfie) {
            $this->selfieRichiesto = true;
            $this->addError('selfie', 'Non è stato possibile confermare che ti trovi alla convention: carica una foto che ritragga te e il cartello del Premio Italia.');

            return;
        }

        if ($errore = $this->erroreUtenteEsistente($dati)) {
            $this->addError('email', $errore);

            return;
        }

        $snapshot = null;

        if ($this->selfie) {
            // Senza posizione dal browser la conferma può arrivare dai dati EXIF del
            // selfie, purché lo scatto sia recente e avvenuto vicino alla convention.
            $exif = ExifPosition::fromFile($this->selfie->getRealPath());

            if ($exif->hasCoordinate()) {
                $posizioneExif = [
                    'lat' => $exif->latitudine,
                    'lng' => $exif->longitudine,
                    'source' => 'exif',
                    'taken_at' => $exif->scattataIl?->toDateTimeString(),
                ];

                if (! $confermata
                    && $exif->recente(self::ETA_MASSIMA_SELFIE)
                    && $this->convention->coordinateVicine($exif->latitudine, $exif->longitudine)) {
                    $confermata = true;
                    $posizione = $posizioneExif;
                } else {
                    $posizione ??= $posizioneExif;
                }
            }

            $snapshot = $this->selfie->store(self::CARTELLA_SELFIE, 'local');
        }

        $omonimo = User::findByName($dati['nome'], $dati['cognome']);

        $application = new Application([
            'convention_id' => $this->convention->id,
            'first_name' => $dati['nome'],
            'last_name' => $dati['cognome'],
            'email' => $dati['email'],
            'user_id' => $omonimo?->id,
            'location' => $posizione,
            'location_confirmed' => $confermata,
            'snapshot' => $snapshot,
            'ip_address' => (string) request()->ip(),
            'user_agent' => (string) request()->userAgent(),
            'status' => $omonimo ? StatoApplicationEnum::DaVerificare : StatoApplicationEnum::InAttesa,
            'notes' => $omonimo
                ? 'Esiste già un elettore con lo stesso nome e cognome ma con un indirizzo email diverso.'
                : null,
        ]);
        $application->save();

        $application->sendConfirmation();

        $this->reset('nome', 'cognome', 'email', 'selfie');
        $this->inviata = true;
    }

    /**
     * Il selfie è comunque richiesto se la convention non ha una location con cui
     * confrontarsi o se dallo stesso dispositivo è già arrivata un'altra richiesta.
     */
    private function selfieSempreRichiesto(): bool
    {
        return ! $this->convention->hasLocation()
            || Application::dispositivoGiaUsato((string) request()->ip(), (string) request()->userAgent());
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
}; ?>

<div>
	@if($inviata)

		<div class="row">
			<div class="col-sm-12">
				<div class="alert alert-success">
					La tua richiesta è stata registrata e sarà valutata al più presto. Ti abbiamo inviato un'email di conferma: se non la trovi, controlla nella cartella spam.
				</div>
			</div>
		</div>

	@else

		<form wire:submit="invia" id="form-applicazione">
			<div class="form-group row">
				<div class="col-sm-12" wire:ignore>
					<div class="alert alert-info" id="geo-stato">Verifica della posizione in corso&hellip;</div>
				</div>
			</div>

			<div class="form-group row mt-3">
				<label class="col-sm-2 col-form-label text-right" for="nome">Nome</label>
				<div class="col-sm-6">
					<input type="text" class="form-control{{ $errors->has('nome') ? ' is-invalid' : '' }}" id="nome" wire:model="nome" placeholder="Nome" required>
					@error('nome')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<div class="form-group row mt-3">
				<label class="col-sm-2 col-form-label text-right" for="cognome">Cognome</label>
				<div class="col-sm-6">
					<input type="text" class="form-control{{ $errors->has('cognome') ? ' is-invalid' : '' }}" id="cognome" wire:model="cognome" placeholder="Cognome" required>
					@error('cognome')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<div class="form-group row mt-3">
				<label class="col-sm-2 col-form-label text-right" for="email">Email</label>
				<div class="col-sm-6">
					<input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" wire:model="email" placeholder="Email" required>
					@error('email')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<div class="form-group row mt-3{{ $selfieRichiesto ? '' : ' d-none' }}" id="blocco-selfie" x-bind:class="{ 'd-none': ! $wire.selfieRichiesto }">
				<label class="col-sm-2 col-form-label text-right" for="selfie">Foto</label>
				<div class="col-sm-6">
					<input type="file" class="form-control-file{{ $errors->has('selfie') ? ' is-invalid' : '' }}" id="selfie" wire:model="selfie" accept="image/*" capture="user">
					<small class="form-text text-muted">Scatta una foto che ritragga te insieme al cartello del Premio Italia.</small>
					<small class="form-text text-muted" wire:loading wire:target="selfie">Caricamento della foto in corso&hellip;</small>
					@error('selfie')
						<div class="invalid-feedback d-block">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<div class="form-group row mt-3">
				<div class="col-sm-10 offset-sm-2">
					<button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="invia, selfie">Invia la richiesta</button>
				</div>
			</div>
		</form>

	@endif
</div>

@script
<script>
	var sede = @json($convention->hasLocation() ? ['lat' => $convention->latitudine(), 'lng' => $convention->longitudine()] : null);
	var raggio = {{ Convention::RAGGIO_APPLICAZIONE }};
	var selfieSempreRichiesto = @json($selfieSempreRichiesto);

	var stato = $wire.$el.querySelector('#geo-stato');

	function messaggio(testo, classe) {
		stato.className = 'alert ' + classe;
		stato.textContent = testo;
	}

	function chiediSelfie(testo) {
		$wire.selfieRichiesto = true;
		messaggio(testo + ' Per completare la richiesta scatta una foto che ritragga te e il cartello del Premio Italia.', 'alert-warning');
	}

	function distanza(lat1, lng1, lat2, lng2) {
		var r = 6371000;
		var dLat = (lat2 - lat1) * Math.PI / 180;
		var dLng = (lng2 - lng1) * Math.PI / 180;
		var a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
			+ Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
		return r * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
	}

	// La posizione viene chiesta una sola volta, al caricamento della componente:
	// l'invio del form non ricarica la pagina e quindi non ripete la richiesta.
	if (!sede) {
		chiediSelfie('Per questa convention non è disponibile la verifica della posizione.');
	} else if (!navigator.geolocation) {
		chiediSelfie('Il tuo browser non permette di rilevare la posizione.');
	} else {
		navigator.geolocation.getCurrentPosition(function (posizione) {
			$wire.latitude = posizione.coords.latitude;
			$wire.longitude = posizione.coords.longitude;

			var metri = distanza(sede.lat, sede.lng, posizione.coords.latitude, posizione.coords.longitude);

			if (metri > raggio) {
				chiediSelfie('Risulti a più di ' + Math.round(metri) + ' metri dalla convention.');
			} else if (selfieSempreRichiesto) {
				chiediSelfie('Da questo dispositivo è già stata inviata un\'altra richiesta.');
			} else {
				$wire.selfieRichiesto = false;
				messaggio('Posizione confermata: risulti alla convention.', 'alert-success');
			}
		}, function () {
			$wire.latitude = null;
			$wire.longitude = null;
			chiediSelfie('Non è stato possibile rilevare la tua posizione.');
		}, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
	}
</script>
@endscript
