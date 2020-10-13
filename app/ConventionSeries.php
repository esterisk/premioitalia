<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ConventionSeries extends Model
{
	protected $table = 'convention_series';
	protected $primaryKey = 'id';

	public function convention()
	{
		return $this->hasMany(Convention::class);
	}
	

}
