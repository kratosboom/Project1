<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimony extends Model
{
    protected $fillable = [
        'author_name',
        'author_role',
        'body',
        'proof_images',
        'rating',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'sort_order' => 'integer',
            'proof_images' => 'array',
        ];
    }
}
