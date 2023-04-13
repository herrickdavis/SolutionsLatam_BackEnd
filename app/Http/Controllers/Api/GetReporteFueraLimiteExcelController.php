<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\FueraLimiteExport;
use App\Models\ClickBotones;
use Throwable;

class GetReporteFueraLimiteExcelController extends Controller
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
        $usuario = $request->user();
        $analytic_click = new ClickBotones;
        $analytic_click->id_user = $usuario->id;
        $analytic_click->id_boton = 26;
        $analytic_click->save();

        $fecha_inicio = $request->fecha_inicio." 00:00:00";
        $fecha_fin = $request->fecha_fin." 23:59:59";
        
        $ver_empresa_sol = $usuario->ver_empresa_sol;
        $ver_contacto_sol = $usuario->ver_contacto_sol;
        $ver_empresa_con = $usuario->ver_empresa_con;
        $ver_contacto_con = $usuario->ver_contacto_con;


        try {
            $sql_fuera_limites = DB::table('muestras as m')
                            ->select(DB::raw(
                                "
                                m.numero_muestra,
                                tm.nombre_tipo_muestra,
                                e.nombre_estacion,
                                DATE_FORMAT(m.fecha_muestreo,'%d/%m/%Y') as fecha_muestreo,
                                CASE
                                MONTH(m.fecha_muestreo)
                                WHEN 1 THEN '".trans('Enero')."'
                                WHEN 2 THEN '".trans('Febrero')."'
                                WHEN 3 THEN '".trans('Marzo')."'
                                WHEN 4 THEN '".trans('Abril')."'
                                WHEN 5 THEN '".trans('Mayo')."'
                                WHEN 6 THEN '".trans('Junio')."'
                                WHEN 7 THEN '".trans('Julio')."'
                                WHEN 8 THEN '".trans('Agosto')."'
                                WHEN 9 THEN '".trans('Setiembre')."'
                                WHEN 10 THEN '".trans('Octubre')."'
                                WHEN 12 THEN '".trans('Noviembre')."'
                                WHEN 12 THEN '".trans('Diciembre')."'
                                END AS mes,
                                p.nombre_parametro,
                                REPLACE(mp.valor,',','.'),
                                u.unidad,
                                CASE 
                                WHEN lp.minimo IS NULL THEN '---'
                                ELSE REPLACE(lp.minimo,',','.') END as minimo,
                                CASE 
                                WHEN lp.maximo IS NULL THEN '---'
                                ELSE REPLACE(lp.maximo,',','.') END as maximo,
                                l.nombre_limite"
                            ))
                            ->leftjoin('muestra_parametros as mp', 'mp.id_muestra', '=', 'm.id')
                            ->leftjoin('unidades as u', 'u.id', '=', 'mp.id_unidad')
                            ->leftjoin('parametros as p', 'p.id', '=', 'mp.id_parametro')
                            ->leftjoin('tipo_muestras as tm', 'tm.id', '=', 'm.id_tipo_muestra')
                            ->leftjoin('estaciones as e', 'e.id', '=', 'm.id_estacion')
                            ->leftjoin('limites as l', 'l.id', '=', 'm.id_limite')
                            ->leftjoin('limite_parametros as lp', function ($query) {
                                $query->on('lp.id_limite', '=', 'm.id_limite')
                                ->on('mp.id_parametro', '=', 'lp.id_parametro');
                            })
                            ->where('m.fecha_muestreo', '>=', $fecha_inicio)
                            ->where('m.fecha_muestreo', '<=', $fecha_fin)
                            ->where('m.activo', '=', 'S')
                            ->where('mp.id_parecer', '=', 3)
                            ->orderBy('m.fecha_muestreo', 'ASC');
                        
            $sql_fuera_limites = filtroMuestrasQuery($sql_fuera_limites,$usuario);
            $sql_fuera_limites = $sql_fuera_limites->distinct()->get();

            $cabecera = ['Numero Muestra', 'Tipo Muestra', 'Estacion', 'Fecha Muestreo', 'Mes','Analito', 'Valor', 'Unidades', 'Limite Minimo', 'Limite Maximo', 'Normativa'];
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return \Excel::download(new FueraLimiteExport($sql_fuera_limites, $cabecera), 'Fuera Limite.xlsx');
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
        //
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
