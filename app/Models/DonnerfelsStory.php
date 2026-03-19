<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonnerfelsStory extends Model
{
    use HasFactory;

    protected $table = 'story_table_donnerfels';

    protected $fillable = [
        'text',
        'image',
        'title',
    ];

}
