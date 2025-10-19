<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $id_empresas = getIdEmpresas($user);
        $validated = $request->validate([
            'per_page' => 'nullable|integer|min:1',
            'leido' => 'nullable|string|in:N,S',
            'nivel_notificacion' => 'nullable|integer',
            'fecha' => 'nullable|date|after_or_equal:1900-01-01',
        ]);

        $per_page = $validated['per_page'] ?? 10;
        $leido = $validated['leido'] ?? null;
        $nivel_notificacion = $validated['nivel_notificacion'] ?? -1;
        $fecha = $validated['fecha'] ?? -1;

        $total = Notificacion::whereIn('empresa_id', $id_empresas)->count();
        $total_sin_leer = Notificacion::where('leido', 'N')->whereIn('empresa_id', $id_empresas)->count();

        $notificaciones = Notificacion::query()
            ->when($leido === 'N', function ($query) use ($leido) {
                return $query->where('leido', $leido);
            })
            ->when($nivel_notificacion != -1, function ($query) use ($nivel_notificacion) {
                return $query->where('nivel_notificacion_id', $nivel_notificacion);
            })
            ->when($fecha != -1, function ($query) use ($fecha) {
                return $query->whereDate('created_at', $fecha);
            })
            ->whereIn('empresa_id', $id_empresas)
            ->paginate($per_page);

        $response = $notificaciones->toArray();
        $response['custom_metadata'] = [
            'total' => $total,
            'total_sin_leer' => $total_sin_leer,
        ];

        return response()->json($response);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($id == -1) {
            Notificacion::where('leido', 'N')->update(['leido' => 'S', 'updated_at' => now()]);    
            return response()->json(['success' => 'Notificación actualizada correctamente']);
        }
        $notificacion = Notificacion::find($id);
        if (!$notificacion) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        $notificacion->update(['leido' => 'S', 'updated_at' => now()]);

        return response()->json(['success' => 'Notificación actualizada correctamente']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
