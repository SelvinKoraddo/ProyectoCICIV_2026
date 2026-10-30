<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'id_movimiento';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        'fecha' => 'datetime', 'cantidad' => 'decimal:2',
        'saldo_anterior' => 'decimal:2', 'saldo_resultante' => 'decimal:2',
    ];
}
