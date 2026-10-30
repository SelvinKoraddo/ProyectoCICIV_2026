<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        'precio_venta' => 'decimal:2',
        'existencia_actual' => 'decimal:2',
        'existencia_minima' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
