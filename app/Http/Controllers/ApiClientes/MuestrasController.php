<?php

namespace App\Http\Controllers\ApiClientes;

use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp;
use App\Models\ClickBotones;

class MuestrasController extends Controller
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
        $url = "http://api-lims.alslatam.com/api/getData";
        $user = $request->user();
        $token = 'xvmkC508o2sxXrA7302NMSBJsD0XCtWunbSi1Mmk0OGBUItToS';
        $id_muestra = $request->id_muestra;
        $numero_grupo = $request->numero_grupo;
        $numero_proceso = $request->numero_proceso;
        $tipo_fecha = $request->tipo_fecha;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        
        $ver_empresa_sol = $user->ver_empresa_sol;
        $ver_contacto_sol = $user->ver_contacto_sol;
        $ver_empresa_con = $user->ver_empresa_con;
        $ver_contacto_con = $user->ver_contacto_con;

        $id_empresas = DB::table('usuario_empresas as us')->select('id_empresa')->where('id_usuario',$user->id)->get();
        $array_id_empresas = [];
        foreach($id_empresas as $id_empresa) {
            array_push($array_id_empresas,$id_empresa->id_empresa);
        }
        if(count($array_id_empresas) == 0) {
            array_push($array_id_empresas,$user->id_empresa);
        }
        
        $string_id_empresas = implode(",",$array_id_empresas);

        if ($ver_empresa_sol == 'S' and $ver_empresa_con == 'S') {
            $filtro_cliente = "AND (ESOL.IDAUXEMPRESA IN ($string_id_empresas) OR ECON.IDAUXEMPRESA IN ($string_id_empresas))";
        } elseif ($ver_empresa_sol == 'S') {
            $filtro_cliente = "AND ESOL.IDAUXEMPRESA IN ($string_id_empresas)";
        } elseif ($ver_empresa_con == 'S') {
            $filtro_cliente = "AND ECON.IDAUXEMPRESA IN ($string_id_empresas)";
        } elseif ($ver_contacto_sol == 'S') {
            $filtro_cliente = "AND CESOL.EMAIL = $user->email";
        } elseif ($ver_contacto_con == 'S') {
            $filtro_cliente = "AND CECON.EMAIL = $user->email";
        }
        
        $query_json = [];
        if($fecha_desde != null and $fecha_hasta != null) {
            if($this->validateDate($fecha_desde) and $this->validateDate($fecha_hasta)) {

                $dateInicio = new DateTime($fecha_desde);
                $dateFin = new DateTime($fecha_hasta);
                $dateDiff = $dateInicio->diff($dateFin);
                if($dateDiff->days <= 60) {
                    if($tipo_fecha == 1) { //Publicacacion
                        /* Analytic API*/
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $user->id;
                        $analytic_click->id_boton = 35;
                        $analytic_click->save();

                        $query_json = [
                            'tipo_fecha' => 1,
                            'fecha_desde' => $fecha_desde,
                            'fecha_hasta' => $fecha_hasta,
                        ];
                    }
                    if($tipo_fecha == 2) { //Muestreo
                        /* Analytic API*/
                        $analytic_click = new ClickBotones;
                        $analytic_click->id_user = $user->id;
                        $analytic_click->id_boton = 34;
                        $analytic_click->save();

                        $query_json = [
                            'tipo_fecha' => 2,
                            'fecha_desde' => $fecha_desde,
                            'fecha_hasta' => $fecha_hasta,
                        ];
                    }
                } else {
                    $rpt['error'] = "error";
                    $rpt['msg'] = "Verificar el rango de fechas, el límite máximo es de 60 días";
                    return $rpt;    
                }
            } else {
                $rpt['error'] = "error";
                $rpt['msg'] = "Verificar formato de fecha Y-m-d H:i:s";
                return $rpt;
            }
        } 
        if($numero_proceso != null and strpos($numero_proceso,'/') !== false ) {
            /* Analytic API*/
            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $user->id;
            $analytic_click->id_boton = 33;
            $analytic_click->save();

            $query_json['numero_proceso'] = $numero_proceso;
        }
        if($numero_grupo != null and strpos($numero_grupo,'/') !== false ) {
            /* Analytic API*/
            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $user->id;
            $analytic_click->id_boton = 32;
            $analytic_click->save();

            $query_json['numero_grupo'] = $numero_grupo;
        }
        if($id_muestra != null) {
            /* Analytic API*/
            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $user->id;
            $analytic_click->id_boton = 31;
            $analytic_click->save();

            $query_json['id_muestra'] = $id_muestra;
        }        
        $query_json['filtro_cliente'] = $filtro_cliente;
        
        $client = new GuzzleHttp\Client();
        $res = $client->request('POST', $url, [
            'json' => $query_json, 'headers' => ['Authorization' => 'Bearer '.$token]
        ]);

        return response($res->getBody())->header('Accept', 'application/json');
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

    function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
