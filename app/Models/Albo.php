<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Albo extends Model
{
	protected $table = 'albo';
	protected $primaryKey = 'id';
	protected $guarded = [];

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}

	public function descrizione_ricca()
	{
		$campi = json_decode($this->campi, true);
		if (!$campi) return "NON BUONO";
		$campi_ricchi = [];
		foreach ($campi as $label => $value) {
			if (in_array($label, ['titolo', 'testata'])) $campi_ricchi[] = '<em>' . $value . '</em>';
			elseif (in_array($label, ['url'])) $campi_ricchi[] = '<a href="' . $value . '">' . $value . '</a>';
			else $campi_ricchi[] = $value;
		}
		return implode(', ', $campi_ricchi);
	}

	public static function consolida($annata)
	{
		$candidati = Candidato::finalista()->whereAnno($annata->anno)->get();
		$conteggi  = ConteggioFinale::whereAnno($annata->anno)->get();
		$inseriti = 0;
		$result = ['status' => 'success'];

		if ($candidati->count() == 0) {
			return ['status' => 'error', 'message' => 'candidati finalisti non trovati'];
		}
		if ($conteggi->count() == 0) {
			return ['status' => 'error', 'message' => 'conteggi finale non trovati'];
		}
		if (Albo::whereAnno($annata->anno)->count() > 0) {
			$rimossi = Albo::whereAnno($annata->anno)->delete();
			$result['deleted'] = $rimossi;
		}


		$p[1] = [];
		$p[2] = [];
		$p[3] = [];

		foreach ($conteggi as $conteggio) {
			$cat = $conteggio->categoria_id;
			$p[1][$cat] = explode(',', $conteggio->vincitori);
			$p[2][$cat] = explode(',', $conteggio->secondi);
			$p[3][$cat] = explode(',', $conteggio->terzi);
		}

		foreach ($candidati as $candidato) {
			$posizione = 0;
			for ($i = 1; $i <= 3; $i++) if (in_array(
				$candidato->id,
				$p[$i][$candidato->categoria_id]
			)) $posizione = $i;

			$albo = new Albo([
				'id' => 0,
				'categoria_id' => $candidato->categoria_id,
				'anno' => $candidato->anno,
				'finalista' => $posizione == 1 ? 2 : 1,
				'descrizione' => $candidato->descrizione,
				'note' => $candidato->note,
				'campi' => $candidato->campi,
				'posizione' => $posizione
			]);
			$albo->save();
			$inseriti++;
		}

		$result['inserted'] = $inseriti;
		return $result;
	}
}