<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Candidato;
use Illuminate\Support\Str;


class Segnalazione extends Model
{
	protected $table = 'segnalazioni';
	protected $primaryKey = 'id';

	public function elettore()
	{
		return $this->belongsTo(User::class);
	}
	
	public function voto()
	{
		return $this->belongsTo(Voto::class);
	}
	
	public function categoria()
	{
		return $this->belogsTo(Categoria::class, 'segnalazione_categoria_id');
	}

	public function candidato()
	{
		return $this->belongsTo(Candidato::class);
	}		

	public function salva($campi)
	{
		$descrizione = substr(implode(' - ', array_values($campi)),0,250);
		$identificativo = md5($this->segnalazione_anno.'-'.$this->segnalazione_categoria_id.'-'.Str::slug($descrizione));

		if (!($candidato = Candidato::whereIdentificativo($identificativo)->first())) {
			$candidato = new Candidato();
			$candidato->categoria_id = $this->segnalazione_categoria_id;
			$candidato->campi = json_encode($campi);
			$candidato->anno = $this->segnalazione_anno;
			$candidato->descrizione = $descrizione;
			$candidato->identificativo = $identificativo;
			$candidato->save();
		}
		$this->categoria_originale_id = $this->segnalazione_categoria_id;
		$this->candidato_originale_id = $candidato->getKey();
		$this->candidato_id = $candidato->spostato_in ?: $candidato->getKey();
		if ($candidato->stato == 'escluso') $this->candidato_id = 0;
		
		$this->save();
	}

}
