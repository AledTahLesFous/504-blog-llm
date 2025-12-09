<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChosenArticle extends Model
{
    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array', // auto JSON <=> array
    ];
}

