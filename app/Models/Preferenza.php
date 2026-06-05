<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Candidato;
use Illuminate\Support\Str;


class Preferenza extends Model
{
	protected $table = 'preferenze';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];

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
		return $this->belongsTo(Categoria::class, 'preferenza_categoria_id');
	}

	public function candidato($posizione = 1)
	{
		return $this->belongsTo(Candidato::class, 'candidato_' . $posizione . '_id');
	}

	public function candidato_1()
	{
		return $this->belongsTo(Candidato::class, 'candidato_1_id');
	}

	public function candidato_2()
	{
		return $this->belongsTo(Candidato::class, 'candidato_2_id');
	}

	public function candidato_3()
	{
		return $this->belongsTo(Candidato::class, 'candidato_3_id');
	}

	public function salva()
	{
		$this->save();
	}
}
