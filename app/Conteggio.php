<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Conteggio extends Model
{
	protected $table = 'conteggi';
	protected $primaryKey = 'id';

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}	
}
