<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;
use App\Mail\Invito;
use App\Mail\VotoFase1;
use App\Mail\VotoFase2;
use App\Mail\SollecitoInvio;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    protected $hidden = [ 'password', 'remember_token' ];

	protected $table = 'users';
	protected $primaryKey = 'id';
    
    static public function findByEmail($email)
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
		if (empty($this->token) || $this->tokenExpired()) $this->createToken();
		return $this->token;
	}
	
	public function createToken()
	{
		$this->token = str_random(20);
		$this->token_expire = date('Y').'-12-31';
		$this->save();
	}
	
	public function sendInvitation($mailing = false)
	{
		$token = $this->getToken();
		$annata = Annata::corrente();
		$fase   = $annata->fase();
		$invitation = $annata->anno.' '.$annata->fase();
		$reminder   = $annata->anno.' '.$annata->fase().' R';
		$tobesent = $invitation;
		
		if ($mailing) { // verifica se inviare o no solo se è un mailing
			if ($this->voto($annata->anno,$fase)) $tobesent = false; // ha già votato, niente invito
			elseif ($this->last_invitation == $reminder) $tobesent = false; // ha già ricevuto un sollecito non lo si disturba ulteriormente
			elseif ($this->last_invitation == $invitation) { 
				if (Carbon::now()->subDays(5)->gt($this->invitation_sent)) $tobesent = $reminder; // sono passati più di 15 giorni dalla prima notifica, manda sollecito
				else $tobesent = false; // non è ancora il momento di sollecitare
			}
		}
	
		if ($tobesent) {	
			$this->last_invitation = $tobesent;
			Mail::to($this)->send(new Invito($this,$annata,$tobesent == $reminder));
			$this->invitation_sent = date('Y-m-d H:i:s');
			$this->save();
			return 1;
		} return 0;
	}
	
	public function sendSollecitoInvio($verificato = false)
	{
		$token = $this->getToken();
		$annata = Annata::corrente();
		$fase   = $annata->fase();
		$invitation = $annata->anno.' '.$annata->fase().' S';
		
		if ($verificato || ($voto = $this->voto($annata->anno,$fase) && $voto->stato = "preparazione")) {
			Mail::to($this)->send(new SollecitoInvio($this,$annata));
			$this->last_invitation = $invitation;
			$this->invitation_sent = date('Y-m-d H:i:s');
			$this->save();
			return 1;
		} return 0;
	}
	
	public function voti()
	{
		return $this->hasMany(Voto::class);
	}
	
	public function voto($anno,$fase)
	{
		return $this->voti()->whereAnno($anno)->whereFase($fase)->first();
	}
	
	public function segnalazioni($anno = null)
	{
		if (!$anno) $anno = date('Y');
		return $this->belongsToMany(Candidato::class, 'segnalazioni', 'user_id', 'candidato_originale_id')->whereAnno($anno)->orderBy('categoria_id');
	}
	
	public function preferenze($anno = null)
	{
		if (!$anno) $anno = date('Y');
		return $this->hasMany(Preferenza::class)->wherePreferenzaAnno($anno)->orderBy('preferenza_categoria_id');
	}
	
	public function partecipazioni()
	{
		return $this->hasMany(Partecipazioni::class);
	}
	
	public function emailVoto1($voto)
	{
		$segnalazioni = $this->segnalazioni($voto->anno)->get();
		echo Mail::to($this)->send(new VotoFase1($this,$voto,$segnalazioni));
	}
	
	public function emailVoto2($voto)
	{
		$preferenze = $this->preferenze($voto->anno)->get();
	//	dd($segnalazioni);
		echo Mail::to($this)->send(new VotoFase2($this,$voto,$preferenze));
	}

	public function unsubscribe()
	{
		$this->user_status = 0;
		$this->status_detail = "Rimosso su richiesta utente ".date('d/m/Y H:s');
		$this->save();
	}
		
	public function choice($voto, $cat, $choice)
	{
		if (!$voto) $v = 0;
		if (!isset($voto[$cat])) $v = 'U';
		if (!isset($voto[$cat][$choice])) $v =  'U';
		$v = $voto[$cat][$choice];
		return (($choice == 1 && $v === 0) ? 'U' : $v);
	}
	
	public function isAdmin()
	{
		return $this->admin > 0;
	}

}
