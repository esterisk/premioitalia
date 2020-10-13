<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ConteggioFinale extends Model
{
	protected $table = 'conteggi_finale';
	protected $primaryKey = 'id';
	protected $fillable = [ 'anno', 'categoria_id' ];

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}	
}
