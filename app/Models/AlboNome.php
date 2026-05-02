<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlboNome extends Model
{
	protected $table = 'albo_nomi';
	protected $primaryKey = 'id';
	protected $guarded = [];

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}

	public static function raccogli()
	{
		$albo = Albo::whereFinalista(2)->get();
		$inseriti = 0;
		$result = ['status' => 'success'];

		if ($albo->count() == 0) {
			return ['status' => 'error', 'message' => 'vincitori non trovati'];
		}
		if (AlboNome::count() > 0) {
			$rimossi = AlboNome::truncate();
			$result['deleted'] = $rimossi;
		}

		foreach ($albo as $v) {
			$vv = json_decode($v->campi);
			if (!empty($vv->nome)) $nome = $vv->nome;
			elseif (!empty($vv->autore)) $nome = $vv->autore;
			else $nome = null;

			if ($nome) {

				$nome = trim($nome);
				$nome = str_replace(' e ', ',', $nome);
				$nome = str_replace(', ', ',', $nome);
				$nomi = explode(',', $nome);

				foreach ($nomi as $n) {
					$albonome = new AlboNome([
						'id' => 0,
						'categoria_id' => $v->categoria_id,
						'anno' => $v->anno,
						'nome' => $n
					]);
					$albonome->save();
					$inseriti++;
				}
			}
		}

		$result['inserted'] = $inseriti;
		return $result;
	}
}