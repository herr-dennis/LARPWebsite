<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charakters extends Model
{
    protected $table ="charakter";

    protected $fillable = [
        'name',
        'text',
        'image'
    ];

}
