<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    /**
     * Relación: Una valoración pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una valoración pertenece a un producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
