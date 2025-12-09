<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChosenArticle extends Model
{
    // Désactiver l'auto-increment car id est un string (MD5)
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'data'];

    protected $casts = [
        'data' => 'array', // auto JSON <=> array
    ];
}

