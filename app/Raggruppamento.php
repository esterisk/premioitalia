<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Raggruppamento extends Model
{
	protected $table = 'raggruppamenti';
	protected $primaryKey = 'id';

	public function categorie()
	{
		return $this->belongsToMany(Categoria::class, 'raggruppamenti_categorie');
	}
	
	public function getRouteKeyName()
	{
		return 'slug';
	}
}
