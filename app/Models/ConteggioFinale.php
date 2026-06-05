<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConteggioFinale extends Model
{
	protected $table = 'conteggi_finale';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}
}