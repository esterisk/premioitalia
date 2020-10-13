<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
	protected $table = 'candidati';
	protected $primaryKey = 'id';

	public function categoria()
	{
		return $this->belongsTo(Categoria::class);
	}

	public function segnalazioni()
	{
		return $this->hasMany(Segnalazione::class);
	}
	
	public function sigla($disambigua = 0)
	{
		static $disambiguatori = [ '', 'X', 'Y', 'Z', 'W', 'K', 'Q', 'J', 'A', 'B', 'C' ];
		$campi = json_decode($this->campi, true);
		if (!empty($campi['autore'])) $v = $campi['autore'];
		elseif (!empty($campi['nome'])) $v = $campi['nome'];
		elseif (!empty($campi['titolo'])) $v = $campi['titolo'];
		$sigla = strtoupper(preg_replace('|^([a-zA-Z])[^, ]*[, ]([a-zA-Z0-9]).+$|', '$1$2', $v));
		if (strlen($sigla) > 2) $sigla = substr($sigla, 0, 2);
		if (strlen($sigla) < 2) $sigla = substr(rand(10, 99), 0, 2);
		if ($disambigua > 0) $sigla = substr($sigla, 0,1).$disambiguatori[$disambigua];
		return $sigla;
	}
		
	public function descrizione_ricca()
	{
		$campi = json_decode($this->campi, true);
		$campi_ricchi = [];
		foreach ($campi as $label => $value) {
			if (in_array($label, ['titolo','testata'])) $campi_ricchi[] = '<em>'.$value.'</em>';
			elseif (in_array($label, [ 'url' ])) $campi_ricchi[] = '<a href="'.$value.'">'.$value.'</a>';
			else $campi_ricchi[] = $value;
		}
		return implode(', ', $campi_ricchi);
	}
	
}
