<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
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
        'comment',
        'is_approved',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Relación: Un comentario pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un comentario pertenece a un producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
