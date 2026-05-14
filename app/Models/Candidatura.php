<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Candidato;
use Illuminate\Support\Str;


class Candidatura extends Model
{
	protected $table = 'candidature';
	protected $primaryKey = 'id';

	protected $guarded = ['id'];

	public function annata()
	{
		return $this->belongsTo(Annata::class, 'anno', 'anno');
	}

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}

	public function motivazione()
	{
		return $this->belongsTo(Motivazione::class, 'motivazione_id');
	}

	public function descrizione_ricca()
	{
		$campi = json_decode($this->campi, true);
		$campi_ricchi = [];
		foreach ($campi as $label => $value) {
			if (in_array($label, ['titolo', 'testata'])) $campi_ricchi[] = '<em>' . $value . '</em>';
			elseif (in_array($label, ['url'])) $campi_ricchi[] = '<em>' . $value . '</em>';
			else $campi_ricchi[] = $value;
		}
		return implode(', ', $campi_ricchi);
	}

	public function scopeValide($query)
	{
		return $query->whereStato('valido')->whereAnno(Annata::corrente()->anno);
	}

	public function scopeNonValide($query)
	{
		return $query->whereStato('escluso')->where('motivo_esclusione', '<>', 'Già presente')->whereAnno(Annata::corrente()->anno);
	}

	public static function salva($annata, $categoria, $request)
	{
		$campi = $request->except('_token');
		$descrizione = substr(implode(' - ', array_values($campi)), 0, 250);
		$identificativo = md5($annata->anno . '-' . $categoria->id . '-' . Str::slug($descrizione));

		if (!($candidatura = Candidatura::whereIdentificativo($identificativo)->first())) {
			$candidatura = new Candidatura();
			$candidatura->categoria_id = $categoria->id;
			$candidatura->campi = json_encode($campi);
			$candidatura->anno = $annata->anno;
			$candidatura->descrizione = $descrizione;
			$candidatura->identificativo = $identificativo;
			$candidatura->stato = 'nuovo';
			$candidatura->save();
			return ['status' => 'success'];
		} else {
			return ['status' => 'error', 'error' => 'Candidatura già presente'];
		}
	}

	public function setCampiAttribute($value)
	{
		$this->attributes['campi'] = json_encode($value);
		$descrizione = substr(implode(' - ', array_values($value)), 0, 250);
		$this->attributes['descrizione'] = $descrizione;
	}

	public function minuscole()
	{
		$campi = json_decode($this->campi, true);
		foreach ($campi as $label => $value) {
			$campi[$label] = Str::of($value)->lower()->ucwords();
		}
		$this->campi = $campi;
		return $this;
	}
}
