<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\Accesso;
use App\Mail\ConfermaIscrizionePreesistente;
use App\Mail\Invito;
use App\Mail\SollecitoInvio;
use App\Mail\SpecialNotice;
use App\Mail\VotoFase1;
use App\Mail\VotoFase2;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser // implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
	protected $guarded = ['id'];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->admin > 0;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public static function findByEmail($email)
    {
        return User::whereEmail($email)->first();
    }

    public function valid()
    {
        return $this->user_status == 1;
    }

    public function scopeIsValid($query)
    {
        return $query->whereUserStatus(1);
    }

    public function tokenExpired()
    {
        return $this->token_expire < date('Y-m-d');
    }

    public function getToken()
    {
        if (empty($this->token) || $this->tokenExpired()) {
            $this->createToken();
        }

        return $this->token;
    }

    public function createToken()
    {
        $this->token = Str::random(20);
        $this->token_expire = date('Y').'-12-31';
        $this->save();
    }

    public function createPassword()
    {
        $this->password = Hash::make(Str::random(20));
    }

    public static function destinatariMailing($tag, $limit = null)
    {
        $votiRicevuti = Voto::where('anno', date('Y'))->where('fase', Annata::corrente()->fase())->pluck('user_id')->toArray();
        $query = User::isValid()->where('last_invitation', '!=', $tag)->whereNotIn('id', $votiRicevuti);
        if ($limit) {
            $query->limit($limit);
        }
        return $query->get();
    }

    public function sendAccess($mailing = false)
    {
        $token = $this->getToken();
        $annata = Annata::corrente();
        $tag = $annata->mailingTag();

        $fase = $annata->fase();
        $invitation = $annata->anno.' '.$annata->fase();
        $reminder = $annata->anno.' '.$annata->fase().' R';
        $tobesent = $tag;

        if ($mailing) { // verifica se inviare o no solo se è un mailing
            if ($this->voto($annata->anno, $fase)) {
                $tobesent = false;
            } // ha già votato, niente invito
            elseif ($this->last_invitation == $tag) {
                $tobesent = false;
            } // ha già ricevuto un sollecito non lo si disturba ulteriormente
            elseif ($this->last_invitation == $invitation) {
                if (Carbon::now()->subDays(5)->gt($this->invitation_sent)) {
                    $tobesent = $tag;
                } // sono passati più di 15 giorni dalla prima notifica, manda sollecito
                else {
                    $tobesent = false;
                } // non è ancora il momento di sollecitare
            }
        }

        if ($tobesent) {
            try {
                if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Address not valid');
                }
                $this->last_invitation = $tobesent;
                Mail::to($this)->send(new Accesso($this, $annata, $tobesent == $reminder));
                $this->invitation_sent = date('Y-m-d H:i:s');
                $this->save();
                if ($mailing) {
                    $annata->accessMailingSending();
                }

                return 1;
            } catch (\Exception $e) {
                $this->email_errors++;
                $this->last_email_error = $e->getMessage();
                $this->save();

                return -1;
            }
        }

        if ($mailing) {
            $annata->accessMailingSending();
        }

        return 0;
    }

    public function sendSollecitoInvio($verificato = false)
    {
        $token = $this->getToken();
        $annata = Annata::corrente();
        $fase = $annata->fase();
        $invitation = $annata->anno.' '.$annata->fase().' S';

        $voto = $this->voto($annata->anno, $fase);
        if ($verificato || ($voto && $voto->stato = 'preparazione')) {
            Mail::to($this)->send(new SollecitoInvio($this, $annata));
            $this->last_invitation = $invitation;
            $this->invitation_sent = date('Y-m-d H:i:s');
            $this->save();

            return 1;
        }

        return 0;
    }

    public function sendRegistrationExistingConfirmation($requester)
    {
        Mail::to($this)->send(new ConfermaIscrizionePreesistente($this, $requester));
    }

    public function sendSpecialNotice($verificato = false)
    {
        $token = $this->getToken();
        $annata = Annata::corrente();
        $fase = $annata->fase();
        $voto = null;

        $voto = $this->voto($annata->anno, $fase);
        if ($verificato || ! ($voto && $voto->stato = 'inviato')) {
            Mail::to($this)->send(new SpecialNotice($this, $annata));

            return 1;
        }

        return 0;
    }

    public function voti()
    {
        return $this->hasMany(Voto::class);
    }

    public function voto($anno, $fase)
    {
        return $this->voti()->whereAnno($anno)->whereFase($fase)->first();
    }

    public function segnalazioni($anno = null)
    {
        if (! $anno) {
            $anno = date('Y');
        }

        return $this->belongsToMany(Candidato::class, 'segnalazioni', 'user_id', 'candidato_originale_id')->whereAnno($anno)->orderBy('categoria_id');
    }

    public function preferenze($anno = null)
    {
        if (! $anno) {
            $anno = date('Y');
        }

        return $this->hasMany(Preferenza::class)->wherePreferenzaAnno($anno)->orderBy('preferenza_categoria_id');
    }

    public function partecipazioni()
    {
        return $this->hasMany(Partecipazioni::class);
    }

    public function emailVoto1($voto)
    {
        $segnalazioni = $this->segnalazioni($voto->anno)->get();
        Mail::to($this)->send(new VotoFase1($this, $voto, $segnalazioni));
    }

    public function emailVoto2($voto)
    {
        $preferenze = $this->preferenze($voto->anno)->get();
        //	dd($segnalazioni);
        Mail::to($this)->send(new VotoFase2($this, $voto, $preferenze));
    }

    public function unsubscribe()
    {
        $this->user_status = 0;
        $this->status_detail = 'Rimosso su richiesta utente '.date('d/m/Y H:s');
        $this->save();
    }

    public function choice($voto, $cat, $choice)
    {
        if (! $voto) {
            $v = 0;
        }
        if (! isset($voto[$cat])) {
            $v = 'U';
        }
        if (! isset($voto[$cat][$choice])) {
            $v = 'U';
        }
        $v = $voto[$cat][$choice] ?? '';

        return ($choice == 1 && $v === 0) ? 'U' : $v;
    }

    public function isAdmin()
    {
        return $this->admin > 0;
    }

    public static function accessMailingStart()
    {
        $annata = Annata::corrente();
        $tag = $annata->mailingTag();
        $elettori = User::destinatariMailing($tag,10);

        ray($tag, count($elettori) . ' destinatari'); 

        if (count($elettori) == 0) {
            $annata->mailingStop();
            return ['status' => 'success', 'message' => 'Nessun destinatario per il mailing'];
        }

        $inviate = 0;
        $error = false;
        $time = time();
        $this_block = 0;

        $consecutive_errors = 0;
        foreach ($elettori as $user) {
            $sent = $user->sendAccess(true);
            if ($sent == -1) {
                $consecutive_errors++;
                if ($consecutive_errors >= 5) {
                    $error = true;

                    return false;
                }
            } else {
                $consecutive_errors = 0;
                $inviate += $user->sendAccess(true);
                $this_block++;
            }
        }
        //sleep(config('premioitalia.mailing.sleep'));
        /*			if ($this_block >= config('premioitalia.mailing.chunks')) {
                        $this_block = 0;
                        sleep(config('premioitalia.mailing.sleep2'));
                    }
        */

        if ($error) {
            $annata->mailingProblem();
        }
        $time = time() - $time;
        $result = ['status' => 'working', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate / $time * 60, 2) : 0];
        return $result;
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function (User $user) {
            if (! $user->name) {
                $user->name = $user->firstname.' '.$user->lastname;
            }
            if (! $user->token) {
                $user->createToken();
            }
            if ($user->user_status === null) {
                $user->user_status = 1;
            }
            if ($user->admin === null) {
                $user->admin = 0;
            }
            if ($user->email_verified === null) {
                $user->email_verified = 0;
            }
            if (! $user->password) {
                $user->createPassword();
            }
            if (! $user->ultimo_voto) {
                $user->ultimo_voto = 0;
            }
            if (! $user->email_errors) {
                $user->email_errors = 0;
            }
        });
    }
}
