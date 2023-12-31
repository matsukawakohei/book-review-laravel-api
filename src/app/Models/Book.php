<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishLists(): HasMany
    {
        return $this->hasMany(WishList::class);
    }
}
