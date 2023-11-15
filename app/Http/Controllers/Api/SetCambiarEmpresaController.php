<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;


class SetCambiarEmpresaController extends Controller
{
    protected $idauxempresas = [];
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
        try {
            $id_empresa = $request->id_empresa;
            
            if($usuario->id_rol < 4) {
                User::where('id',$usuario->id)->update(
                    ['id_empresa' => $id_empresa]
                );
                $usuario->id_empresa = $id_empresa;
                $sql_matrices = DB::table('muestras AS m')
                ->select(DB::raw(
                    "CAST(CASE 
                    WHEN gm.nombre_grupo_matriz is null then CONCAT('998',mx.id) else CONCAT('999',gm.id) end AS UNSIGNED) as id,
                    CASE
                    WHEN gm.nombre_grupo_matriz is null then mx.nombre_matriz else gm.nombre_grupo_matriz end as nombre_matriz"
                ))
                ->leftjoin('matrices AS mx', 'mx.id', '=', 'm.id_matriz')
                ->leftjoin('matriz_grupo_matrices AS mgm', 'mgm.id_matriz', '=', 'm.id_matriz')
                ->leftjoin('grupo_matrices AS gm', 'gm.id', '=', 'mgm.id_grupo_matriz')
                ->orderBy('mx.nombre_matriz', 'ASC')->distinct();

                $sql_matrices = filtroMuestrasQuery($sql_matrices,$usuario);

                $sql_matrices = $sql_matrices->get();

                $rpta['success'] = 'Ok';
                $rpta['mensaje'] = 'Ok';
                $rpta['menu'] = $sql_matrices;
            } else {
                $rpta['error'] = 'error';
                $rpta['mensaje'] = 'no autorizado'; 
            }
        } catch (Throwable $e) {
            report($e);
            $mensaje = $e->getMessage();
            $rpta['error'] = 'error';
            $rpta['mensaje'] = $mensaje;
        }
        
        return $rpta;
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
