<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcklandStory extends Model
{
         use HasFactory;
    protected $table ="story_table_eckland";
    protected $fillable = [
        'text',
        'image',
        'title',
        'position'
    ];
}
