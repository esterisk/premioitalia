<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Annata extends Model
{
	protected $table = 'annate';
	protected $primaryKey = 'id';

	static public function corrente()
	{
		return Annata::orderBy('anno','desc')->first();
	}
	
	public function fase()
	{
		$now = date('Y-m-d');
		if ($now < $this->fase_1_da) {
			return 'pre';
		} elseif ($now >= $this->fase_1_da && $now <= $this->fase_1_a.' 23:59:59') {
			return 'fase1';
		} elseif ($now > $this->fase_1_a.' 23:59:59' && $now < $this->fase_2_da) {
			return 'spoglio1';
		} elseif ($now >= $this->fase_2_da && $now <= $this->fase_2_a.' 23:59:59') {
			return 'fase2';
		} elseif ($now > $this->fase_2_a.' 23:59:59' && $now < $this->premiazione) {
			return 'spoglio2';
		} elseif ($now > $this->premiazione) {
			return 'post';
		}
		return 'pre';
	}
	
	public function candidatureAperte() {
		$now = date('Y-m-d');
		return ($now >= $this->candidature_da && $now <= $this->candidature_a.' 23:59:59');
	}
	
	public function voti()
	{
		return $this->hasMany(Voto::class, 'anno', 'anno');
	}
	
	public function segnalazioni()
	{
		return $this->hasMany(Segnalazione::class, 'segnalazione_anno', 'anno');
	}
	
	public function preferenze()
	{
		return $this->hasMany(Preferenza::class, 'preferenza_anno', 'anno');
	}
	
	public function finalisti()
	{
		return $this->finalisti_pubblici > 0;
	}
	
	public function risultati()
	{
		return $this->risultati_pubblici > 0;
	}
	
	public function getConventionAttribute()
	{
		return Convention::whereAnno($this->anno)->where('italcon','>',0)->first();
	}
	
}
