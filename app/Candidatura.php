<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Candidato;
use Illuminate\Support\Str;


class Candidatura extends Model
{
	protected $table = 'candidature';
	protected $primaryKey = 'id';

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}

	public function descrizione_ricca()
	{
		$campi = json_decode($this->campi, true);
		$campi_ricchi = [];
		foreach ($campi as $label => $value) {
			if (in_array($label, ['titolo','testata'])) $campi_ricchi[] = '<em>'.$value.'</em>';
			elseif (in_array($label, [ 'url' ])) $campi_ricchi[] = '<em>'.$value.'</em>';
			else $campi_ricchi[] = $value;
		}
		return implode(', ', $campi_ricchi);
	}
	
	public function scopeValide($query)
	{
		return $query->whereStato('valido')->whereAnno(Annata::corrente()->anno);
	}

	public static function salva($annata, $categoria, $request)
	{
		$campi = $request->except('_token');
		$descrizione = substr(implode(' - ', array_values($campi)),0,250);
		$identificativo = md5($annata->anno.'-'.$categoria->id.'-'.Str::slug($descrizione));

		if (!($candidatura = Candidatura::whereIdentificativo($identificativo)->first())) {
			$candidatura = new Candidatura();
			$candidatura->categoria_id = $categoria->id;
			$candidatura->campi = json_encode($campi);
			$candidatura->anno = $annata->anno;
			$candidatura->descrizione = $descrizione;
			$candidatura->identificativo = $identificativo;
			$candidatura->stato = 'nuovo';
			$candidatura->save();
			return [ 'status' => 'success' ];
		} else {
			return [ 'status' => 'error', 'error' => 'Candidatura già presente' ];
		}
		
	}

}
