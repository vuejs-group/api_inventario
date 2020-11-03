<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    
	public $timestamps = false;
	
	protected $table = 'def_bodegas';
	
	protected $fillable = [
		'id',
		'nombre',
		'estado'
	];
}
