<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conteggio extends Model
{
	protected $table = 'conteggi';
	protected $primaryKey = 'id';

	public $conti_candidati;
	public $indicatori;

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}
}