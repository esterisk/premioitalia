<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motivazione extends Model
{
    protected $table = 'motivazioni';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
