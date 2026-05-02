<?php

namespace App\Models;

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