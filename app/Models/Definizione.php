<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Definizione extends Model
{
	protected $table = 'definizioni';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];
}