<?php

namespace App\Http\Controllers\DataExterna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelDataExternaImport;
use Illuminate\Support\Facades\DB;

use Throwable;
class SetDataExternaArchivoController extends Controller
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
        set_time_limit(1200);
        $file = $request->file('excel');
        $usuario = $request->user();
        try {
            DB::table('data_externa_temporals')->where('id_user', $usuario->id)
                                                ->where(function($query) {
                                                    $query->whereNull('id_muestra')
                                                        ->orWhereNull('id_matriz')
                                                        ->orWhereNull('id_tipo_muestra')
                                                        ->orWhereNull('id_proyecto')
                                                        ->orWhereNull('id_estacion')
                                                        ->orWhereNull('id_empresa_contratante')
                                                        ->orWhereNull('id_empresa_solicitante')
                                                        ->orWhereNull('id_parametro');
                                                    })->delete();
            Excel::import(new ExcelDataExternaImport($usuario->id), $file);
            $rpta["success"] = "Ok";
            $rpta["mensaje"] = "Ok";
        } catch (Throwable $e) {
            report($e);
            $rpta["error"] = "error";
            $rpta["mensaje"] = $e->getMessage();
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
