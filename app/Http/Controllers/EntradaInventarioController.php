<?php

namespace App\Http\Controllers;

use App\Models\DetalleEntrada;
use App\Models\EntradaInventario;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EntradaInventarioController extends Controller
{
    public function create(): View
    {
        $proveedores = Proveedor::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'entradas.create',
            compact('proveedores', 'productos')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_proveedor' => [
                'required',
                Rule::exists(
                    'proveedores',
                    'id_proveedor'
                )->where(
                    fn ($query) => $query->where('activo', true)
                ),
            ],
            'fecha' => [
                'required',
                'date',
            ],
            'referencia' => [
                'nullable',
                'string',
                'max:50',
            ],
            'productos' => [
                'required',
                'array',
                'min:1',
            ],
            'productos.*.id_producto' => [
                'required',
                'distinct',
                Rule::exists(
                    'productos',
                    'id_producto'
                )->where(
                    fn ($query) => $query->where('activo', true)
                ),
            ],
            'productos.*.cantidad' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'productos.*.costo_unitario' => [
                'required',
                'numeric',
                'gte:0',
            ],
        ]);

        $usuarioId = $request->user()->getAuthIdentifier();

        $entrada = DB::transaction(
            function () use ($data, $usuarioId) {
                $entrada = EntradaInventario::create([
                    'numero' => $this->generarNumero(),
                    'fecha' => $data['fecha'],
                    'referencia' => $data['referencia'] ?? null,
                    'total' => 0,
                    'estado' => 'CONFIRMADA',
                    'id_proveedor' => $data['id_proveedor'],
                    'id_usuario' => $usuarioId,
                ]);

                $total = 0;

                foreach ($data['productos'] as $item) {
                    $producto = Producto::query()
                        ->whereKey($item['id_producto'])
                        ->where('activo', true)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $cantidad = round(
                        (float) $item['cantidad'],
                        2
                    );

                    $costo = round(
                        (float) $item['costo_unitario'],
                        2
                    );

                    $subtotal = round(
                        $cantidad * $costo,
                        2
                    );

                    $saldoAnterior = round(
                        (float) $producto->existencia_actual,
                        2
                    );

                    $saldoResultante = round(
                        $saldoAnterior + $cantidad,
                        2
                    );

                    $detalle = DetalleEntrada::create([
                        'cantidad' => $cantidad,
                        'costo_unitario' => $costo,
                        'subtotal' => $subtotal,
                        'id_entrada' => $entrada->id_entrada,
                        'id_producto' => $producto->id_producto,
                    ]);

                    $producto->update([
                        'existencia_actual' => $saldoResultante,
                    ]);

                    MovimientoInventario::create([
                        'fecha' => $data['fecha'],
                        'tipo' => 'RECEPCION',
                        'cantidad' => $cantidad,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_resultante' => $saldoResultante,
                        'referencia' => $entrada->numero,
                        'id_producto' => $producto->id_producto,
                        'id_detalle_venta' => null,
                        'id_detalle_entrada' =>
                            $detalle->id_detalle_entrada,
                    ]);

                    $total += $subtotal;
                }

                $entrada->update([
                    'total' => round($total, 2),
                ]);

                return $entrada;
            }
        );

        return redirect()
            ->route('entradas.create')
            ->with(
                'entrada_confirmada',
                $entrada->numero
            );
    }

    private function generarNumero(): string
    {
        return 'ENT'
            . now()->format('ymdHis')
            . Str::upper(Str::random(3));
    }
}