<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Albo extends Model
{
	protected $table = 'albo';
	protected $primaryKey = 'id';

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
			if (in_array($label, ['titolo','testata'])) $campi_ricchi[] = '<em>'.$value.'</em>';
			elseif (in_array($label, [ 'url' ])) $campi_ricchi[] = '<a href="'.$value.'">'.$value.'</a>';
			else $campi_ricchi[] = $value;
		}
		return implode(', ', $campi_ricchi);
	}
	
	
}
