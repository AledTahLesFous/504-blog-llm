<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    // Désactiver l’auto-increment car id sera MD5
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',       // MD5 de l'URL
        'title',
        'subtitle',
        'content',
        'published',
        'published_at',
        'url'       // stocker l’URL originale
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
