<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Partecipazioni extends Model
{
	protected $table = 'partecipazioni';
	protected $primaryKey = 'id';

	public function elettore()
	{
		return $this->belogsTo(User::class);
	}
	
	public function convention()
	{
		return $this->belogsTo(Convention::class);
	}
	
}
