<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
    * mass assigment
    */
    protected $guarded = [];
    public $incrementing = false; //show UUID Response
    protected $casts = [
        'created_at' => 'datetime:Y/m/d H:i:s',
        'updated_at' => 'datetime:Y/m/d H:i:s',
    ];
}