<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Muestras; //obligatorio
use App\Models\LogUpload;

use Throwable;

class SetEstadoMuestrasController extends Controller
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
        $data_muestras = $request->json()->all();

        $hubo_error = false;
        
        foreach ($data_muestras as $data) {
            try {
                $datetime = new \DateTime('NOW');
                $datetime = $datetime->format('Y-m-d H:i:s.u');
                $hash = md5($datetime);

                $log_upload = new LogUpload;
                $log_upload->id_user = 1;
                $log_upload->ip = $request->ip();
                $log_upload->url = 'SetEstadoMuestras';
                $log_upload->hash = $hash;
                $log_upload->payload = json_encode($data);

                $log_upload->id_muestra = $data['cdamostra'];
                $log_upload->id_grupo = $data['id_grupo'];

                $id_muestra = $data['cdamostra'];
                $id_estado = $data['id_estado'];

                //Si ya tiene informe no sigo cambiando el estado de la muestra
                $muestra = Muestras::where('id', '=', $id_muestra)->first();
                if ($muestra != null) {
                    if ($muestra->id_certificado != null) {
                        $id_estado = 4;
                    }
                }
                $muestra->id_estado = $id_estado;
                $muestra->save();
                //$log_upload->save();
            } catch (Throwable $e) {
                report($e);
                $hubo_error = true;
                $mensaje = $e->getMessage();
                $log_upload->exception = $e->getMessage();
                $log_upload->save();
            }
        }
        if ($hubo_error) {
            $rpta['error'] = "error";
            $rpta['mensaje'] = $mensaje;

            return $rpta;
        } else {
            $rpta['success'] = "success";
            $rpta['mensaje'] = "Ok";
            return $rpta;
        }
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
