<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'brand',
        'sku',
        'stock',
        'images',
        'is_active',
        'user_id',
    ];

    /**
     * Obtener los atributos que deben ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'images' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relación: Un producto pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un producto puede tener muchas valoraciones
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Relación: Un producto puede tener muchos comentarios
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Calcular el promedio de valoraciones del producto
     */
    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    /**
     * Contar el total de valoraciones del producto
     */
    public function totalRatings()
    {
        return $this->ratings()->count();
    }
}
