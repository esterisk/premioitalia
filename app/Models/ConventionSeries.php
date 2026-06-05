<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConventionSeries extends Model
{
	protected $table = 'convention_series';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];

	public function convention()
	{
		return $this->hasMany(Convention::class);
	}
}