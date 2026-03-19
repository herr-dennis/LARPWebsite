<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Rubric extends Model
{
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }
    use HasFactory;
    protected $fillable = [
        'rubric_verfasser',
        'rubric_name',
        'rubric_status'

    ];


}
