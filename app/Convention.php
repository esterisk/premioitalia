<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Convention extends Model
{
	protected $table = 'convention';
	protected $primaryKey = 'id';

	public function albo()
	{
		return $this->hasMany(Albo::class, 'anno', 'anno');
	}
	
	public function serie()
	{
		return $this->belongsTo(ConventionSeries::class, 'serie_id');
	}

	public function scopeValidePerVoto($query)
	{	
		return $query->where('codice','<>','')->orderBy('anno','desc');
	}

}
