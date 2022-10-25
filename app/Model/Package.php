<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{

    public $table = "packages";
    
  	protected $fillable = [
  		"location",
  		"code",
  		"type",
  		"kg",
  		"route",
  		"list_id",
  		"user_id",
  	];

}
