<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_author',
        'topic_id',
        'post_text'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
