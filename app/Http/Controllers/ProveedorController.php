<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar'));

        $proveedores = Proveedor::query()
            ->with([
                'entradas' => fn ($query) => $query->orderByDesc('fecha'),
            ])
            ->when(
                $buscar !== '',
                fn ($query) => $query->where(
                    fn ($subquery) => $subquery
                        ->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('nit', 'like', "%{$buscar}%")
                        ->orWhere('correo', 'like', "%{$buscar}%")
                )
            )
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view(
            'proveedores.index',
            compact('proveedores', 'buscar')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);
        $data['activo'] = true;

        Proveedor::create($data);

        return redirect()
            ->route('proveedores.index')
            ->with('ok', 'Proveedor registrado correctamente.');
    }

    public function cambiarEstado(
        Proveedor $proveedor
    ): RedirectResponse {
        $proveedor->update([
            'activo' => ! $proveedor->activo,
        ]);

        return back()->with(
            'ok',
            'Estado del proveedor actualizado correctamente.'
        );
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
            ],
            'nit' => [
                'required',
                'string',
                'max:20',
                Rule::unique('proveedores', 'nit'),
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:15',
            ],
            'correo' => [
                'nullable',
                'email',
                'max:100',
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);
    }
}