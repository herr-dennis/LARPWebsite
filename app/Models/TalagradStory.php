<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TalagradStory extends Model
{
    protected $table ="story_table_talagrad";

    use HasFactory;
    protected $fillable = [
        'text',
        'image',
        'title',
        'position'
    ];

}
