<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChosenArticle extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'url', 'data'];

    protected $casts = [
        'data' => 'array',
    ];
}


