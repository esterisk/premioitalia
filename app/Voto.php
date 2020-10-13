<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Categoria;
use App\Candidato;
use App\Segnalazione;
use Exception;

class Voto extends Model
{
	protected $table = 'voto';
	protected $primaryKey = 'id';

	static public function corrente()
	{
		return Annata::orderBy('anno','desc')->first();
	}
	
	public function elettore()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	public function scopeFase1($query) { return $query->whereFase('fase1'); }
	public function scopeFase2($query) { return $query->whereFase('fase2'); }
	public function scopeInviato($query) { return $query->whereStato('inviato'); }
	
	static public function salva($user, $annata, $dati)
	{
    	$voto = $user->voto($annata->anno, $annata->fase());
    	
    	if (!$voto) {
    		$voto = new Voto();
    		$voto->stato = 'preparazione';
    	}
    	if ($voto->stato != 'preparazione') throw new Exception('Voto già inviato, non è più possibile modificarlo');
    	
		$voto->user_id = $user->getKey();
		$voto->anno = $annata->anno;
		$voto->fase = $annata->fase();
		$voto->dati = json_encode($dati);
		$voto->save();

		return $voto;		
	}
	
	private function cleanString($string)
	{
		$s0 = $string;
		$s1 = $string = preg_replace_callback('|([A-Z]{2,})|', function($m) { return ucfirst(strtolower($m[1])); }, $string);
		$s2 = $string = preg_replace('|[\t\n\r ]+|',' ',$string);
		$s2 = $string = str_replace('"','',$string);
		$s2 = $string = str_replace('“','',$string);
		$s2 = $string = str_replace('”','',$string);
		$s2 = $string = str_replace('«','',$string);
		$s2 = $string = str_replace('»','',$string);
		$s3 = $string = trim($string);
		return $string;
	}
	
	public function invia()
	{
    	if ($this->stato != 'preparazione') throw new Exception('Voto già inviato, non è più possibile modificarlo');
		switch ($this->fase) {
			case 'fase1': $this->invia1(); break;
			case 'fase2': $this->invia2(); break;			
		}		
	}

	public function invia1($email = true)
	{
		$dati = json_decode($this->dati, true);
		$voti = [];
		foreach ($dati as $label => $value) {
			if ($value && preg_match('/^([a-z0-9_-]+)-(\d+)-([a-z]+)$/', $label, $m)) {
				$voti[$m[1]][intval($m[2])][$m[3]] = $this->cleanString($value);
			}
		}
		
		Segnalazione::whereUserId($this->user_id)->whereSegnalazioneAnno($this->anno)->delete();
		$segnalazioni = [];
				
		foreach (Categoria::attive() as $cat) {
			$campi = $cat->campi;
			if (!empty($voti[$cat->slug])) {
				for ($i=1; $i<=\Config::get('premioitalia.numero_segnalazioni'); $i++) if (!empty($voti[$cat->slug][$i])) {
					$segnalazione = new Segnalazione();
					$segnalazione->user_id = $this->user_id;
					$segnalazione->voto_id = $this->id;
					$segnalazione->segnalazione_categoria_id = $cat->getKey();
					$segnalazione->segnalazione_anno = $this->anno;
					$segnalazione->salva($voti[$cat->slug][$i]);
				}
			}
		}

		if ($this->stato != 'inviato') {
			$this->stato = 'inviato';
			$this->sent_at = date('Y-m-d H:i:s');
			$this->save();
		}
		if ($email) $this->elettore->emailVoto1($this);
		return true;
	}
	
	public function creaPreferenza($categoria_id, $candidato_1_id, $candidato_2_id, $candidato_3_id)
	{
		$preferenza = new Preferenza();
		$preferenza->user_id = $this->user_id;
		$preferenza->voto_id = $this->id;
		$preferenza->preferenza_categoria_id = $categoria_id;
		$preferenza->preferenza_anno = $this->anno;
		$preferenza->candidato_1_id = $candidato_1_id;
		$preferenza->candidato_2_id = $candidato_2_id;
		$preferenza->candidato_3_id = $candidato_3_id;
		$preferenza->stato = 0;
		$preferenza->salva();
	}

	public function invia2($email = true)
	{
		$dati = json_decode($this->dati, true);
				
		foreach (Categoria::attive() as $cat) {
			if (!empty($dati[$cat->slug])) {
				if ($dati[$cat->slug][1] === '0') continue;
				if ($dati[$cat->slug][1] === 'N') {
					$this->creaPreferenza($cat->getKey(), -1, 0, 0);
				} else {
					$this->creaPreferenza($cat->getKey(), intval($dati[$cat->slug][1]), intval($dati[$cat->slug][2]), intval($dati[$cat->slug][3]));
				}
			}
		}
		
		if ($this->stato != 'inviato') {
			$this->stato = 'inviato';
			$this->sent_at = date('Y-m-d H:i:s');
			$this->save();
		}
		if ($email) $this->elettore->emailVoto2($this);
		return true;
	}

	public static function redo1()
	{
		Candidato::where('id','>',0)->delete();
		Segnalazione::where('id','>',0)->delete();
		foreach (Voto::whereAnno(date('Y'))->whereFase('fase1')->whereStato('inviato')->orderBy('sent_at')->get() as $voto) {
			$voto->invia1(false);
		}
	}
	
}
