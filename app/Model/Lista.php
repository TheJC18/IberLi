<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{

    public $table = "lists";
    
  	protected $fillable = [
  		"estado",
  		"user_id",
  	];

}
