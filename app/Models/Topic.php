<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topic extends Model{
    use HasFactory;

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    public function posts()
    {
        return $this->hasMany(Posts::class);
    }
    protected $fillable =[

        'topic_name',
        'topic_verfasser',
        'rubric_id',
        'topic_status',
        ];
}
