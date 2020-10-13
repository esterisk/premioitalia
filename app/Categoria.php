<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
	protected $table = 'categorie';
	protected $primaryKey = 'id';

	public function scopeActive($query)
	{
		return $query->whereAttiva(1);
	}
	
	public function getRouteKeyName()
	{
		return 'slug';
	}

	static public function attive()
	{
		return Categoria::active()->orderBy('ordine')->get();
	}

	public function regole()
	{
		return $this->belongsToMany('App\Definizione', 'definizioni_categorie', 'categoria_id', 'definizione_id');
	}
	
	public function getCampiAttribute()
	{
		return explode(',',$this->attributes['campi']);
	}
	
	public function fieldLabel($iterazione, $campo)
	{
		return $this->slug.'-'.$iterazione.'-'.$campo;
	}
	
	public function candidaturaLabel($campo)
	{
		return $this->slug.'-'.$campo;
	}
	
	public function nominationLabel($candidato_id)
	{
		return $this->slug.'-'.$candidato_id;
	}
	
	public function conteggio()
	{
		return $this->hasOne(Conteggio::class);
	}
	
	public function conteggioFinale()
	{
		return $this->hasOne(ConteggioFinale::class);
	}
	
	public function candidati()
	{
		return $this->hasMany(Candidato::class);
	}
	
	public function candidature()
	{
		return $this->hasMany(Candidatura::class);
	}
		
	public function finalisti($anno = false, $escludi_vincitori = false)
	{
		$query = $this->candidati()->whereStato('valido')->where('finalista',($escludi_vincitori ? '=' : '>='),1)->orderBy('descrizione');
		if ($anno) $query = $query->whereAnno($anno);
		return $query;
	}
	
	public function vincitori($anno = false)
	{
		$query = $this->candidati()->whereStato('valido')->where('finalista','=',2)->orderBy('descrizione');
		if ($anno) $query = $query->whereAnno($anno);
		return $query;
	}
	
	public function ultimiVincitori($anno)
	{
		return $this->vincitori()->where('anno','<',$anno)->where('anno','>=',$anno-$this->esclusi_ultimi)->orderBy('anno')->get();
	}
	
	public function raggruppamento()
	{
		return $this->belongsToMany(Raggruppamento::class, 'raggruppamenti_categorie');
	}

}
