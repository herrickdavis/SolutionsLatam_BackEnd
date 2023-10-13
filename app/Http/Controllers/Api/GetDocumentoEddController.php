<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\EddExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Edd;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class GetDocumentoEddController extends Controller
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
        $filtros = $request->filtros;
        $filtros = $filtros['filtros'];
        $id_planilla = $request->id;

        $id_empresas = getIdEmpresas($user);

        $ver_empresa_sol = $user->ver_empresa_sol;
        $ver_contacto_sol = $user->ver_contacto_sol;
        $ver_empresa_con = $user->ver_empresa_con;
        $ver_contacto_con = $user->ver_contacto_con;

        $filtro_usuario = '';
        if ($ver_empresa_sol == 'S' and $ver_empresa_con == 'S') {
            $filtro_usuario .= "(m.id_empresa_sol IN (".implode(",",$id_empresas).") OR m.id_empresa_con IN (".implode(",",$id_empresas)."))";
        } elseif ($ver_empresa_sol == 'S') {
            $filtro_usuario .= 'm.id_empresa_sol IN ('.implode(",",$id_empresas).")";
        } elseif ($ver_empresa_con == 'S') {
            $filtro_usuario .= 'm.id_empresa_con IN ('.implode(",",$id_empresas).")";
        } elseif ($ver_contacto_sol == 'S') {
            $filtro_usuario .= "m.id_user_sol ='".$user->id."'";
        } elseif ($ver_contacto_con == 'S') {
            $filtro_usuario .= "m.id_user_con = '".$user->id."'";
        }

        if(strpos($id_planilla, "E") !== false) {
            $select = "SELECT DISTINCT
                        m.id as codigo_muestra\n
                        ";
        } else {
            $select = "SELECT
                        m.id as codigo_muestra,
                        m.numero_muestra as numero_muestra,
                        m.fecha_muestreo as fecha_muestreo,
                        m.fecha_publicacion as fecha_publicacion,
                        concat(p.numero,'/',p.anho) as numero_proceso,
                        pp.nombre_proyecto as proyecto,
                        mt.desc_metodo as metodo,
                        mt.referencia_metodo as referencia_metodo,
                        mp.valor as resultado,
                        u.unidad as unidad,
                        pa.nombre_parametro,
                        ma.nombre_matriz,
                        e.nombre_estacion as nombre_estacion,
                        tm.nombre_tipo_muestra as nombre_tipo_muestra,
                        concat(gm.numero_grupo,'/',gm.anho_grupo) as numero_grupo,
                        l.nombre_limite,
                        lp.maximo as limite_superior,
                        lp.minimo as limite_inferior\n";
        }

        $query_principal = $select."
                            FROM muestras as m
                            LEFT JOIN proceso_muestras as pm on pm.id_muestra = m.id AND pm.id_proceso = (SELECT max(id_proceso) FROM proceso_muestras pm2 WHERE pm2.id_muestra = m.id)
                            LEFT JOIN procesos as p on p.id = pm.id_proceso
                            LEFT JOIN proceso_proyectos as pp on pm.id_proceso = pp.id_proceso
                            LEFT JOIN muestra_grupo_muestras as mgm on mgm.id_muestra = m.id
                            LEFT JOIN grupo_muestras as gm on gm.id = mgm.id_grupo_muestra
                            LEFT JOIN muestra_metodos as mm on mm.id_muestra = m.id
                            LEFT JOIN metodos as mt on mt.id = mm.id_metodo
                            LEFT JOIN muestra_parametros as mp on mm.id_metodo = mp.id_metodo and mp.id_muestra = m.id
                            LEFT JOIN unidades as u on u.id = mp.id_unidad
                            LEFT JOIN parametros as pa on pa.id = mp.id_parametro
                            LEFT JOIN matrices as ma on ma.id = m.id_matriz
                            LEFT JOIN estaciones as e on e.id = m.id_estacion
                            LEFT JOIN tipo_muestras as tm on tm.id = m.id_tipo_muestra
                            LEFT JOIN limites as l on l.id = m.id_limite
                            LEFT JOIN limite_parametros lp on lp.id_limite = l.id and p.id = lp.id_parametro
                            WHERE
                            m.id_estado IN (3,4) AND ";

        foreach ($filtros as $filtro) {
            $pre_cabecera = $filtro['cabecera'];
            $condicion = $filtro['condicion'];
            $valor = $filtro['valor'];
            switch ($pre_cabecera) {
                case strtolower(trans('texto.codigo_muestra')):                    
                    $query_principal = $this->filtros($query_principal, 'm.id', $condicion, $valor);
                    break;
                case strtolower(trans('texto.numero_grupo')):
                    $query_principal = $this->filtros($query_principal, 'numero_grupo', $condicion, $valor);
                    break;
                case strtolower(trans('texto.numero_muestra')):                    
                    $query_principal = $this->filtros($query_principal, 'm.numero_muestra', $condicion, $valor);
                    break;
                case strtolower(trans('texto.Proyecto')):
                    $query_principal = $this->filtros($query_principal, 'p.nombre_proyecto', $condicion, $valor);
                    break;
                case mb_strtolower(trans('texto.Estacion'), 'UTF-8'):
                    $query_principal = $this->filtros($query_principal, 'e.nombre_estacion', $condicion, $valor);
                    break;
                case strtolower(trans('texto.Tipo_Muestra')):
                    $query_principal = $this->filtros($query_principal, 'ta.nombre_tipo_muestra', $condicion, $valor);
                    break;
                case strtolower(trans('texto.Solicitante')):
                    $query_principal = $this->filtros($query_principal, 'esol.nombre_empresa', $condicion, $valor);
                    break;
                case strtolower(trans('texto.Contratante')):
                    $query_principal = $this->filtros($query_principal, 'econ.nombre_empresa', $condicion, $valor);
                    break;
                case strtolower(trans('texto.Fecha_Muestreo')):
                    $query_principal = $this->filtros($query_principal, 'm.fecha_muestreo', $condicion, $valor);
                    break;
                default:
                    # code...
                    break;
            }
        }
        $query_principal .= $filtro_usuario;

        if(strpos($id_planilla, "E") !== false) {
            $results = DB::select($query_principal);
            $id_muestras = array_column($results, 'codigo_muestra');
            
            $id_planilla = str_replace("E", "", $id_planilla);
            $url = "http://api-lims.alslatam.com/api/getDocumento";
            $token = 'xvmkC508o2sxXrA7302NMSBJsD0XCtWunbSi1Mmk0OGBUItToS';

            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
            ];

            $jsonParams = [
                'id_reporte' => $id_planilla,
                'Id_grupo' => '83984',
                'Anio' => '2023',
                'Id_muestras' => implode(",", $id_muestras),
                // Agrega otros parámetros según tus necesidades
            ];

            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $jsonParams,
            ]);

            if ($response->getStatusCode() === 200) {
                $fileContents = $response->getBody()->getContents();

                $contentDisposition = $response->getHeaderLine('Content-Disposition');

                $matches = [];
                if (preg_match('/filename="(.+)"$/', $contentDisposition, $matches)) {
                    $fileName = $matches[1];
                } else {
                    $fileName = 'archivo_sin_nombre';
                }

                return response($fileContents, 200)
                        ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            } else {
                return response()->json(['error' => 'La solicitud no pudo completarse'], 500);
            }
        } else {
            $edd = Edd::where('id','=',$id_planilla)->first();
            $cabeceras = json_decode($edd->configuracion, true);

            $fileName = 'desired_filename.xlsx';

            return Excel::download(new EddExport($query_principal, $cabeceras), 'desired_filename.xlsx');
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

    public function filtros($query_principal, $nombre_columna, $condicion, $valor)
    {        
        switch ($condicion) {
            case 'contiene':
                $query_principal .= $nombre_columna." like '%".$valor."%' and ";
                break;
            
            case 'no contiene':
                $query_principal .= $nombre_columna." not like '%".$valor."%' and ";
                break;

            case 'igual a':
                if ($nombre_columna == "m.fecha_muestreo") {
                    $query_principal .= $nombre_columna." > '".$valor." 00:00:00' and ".$nombre_columna." < '".$valor." 23:59:59' and ";
                } else {
                    $query_principal .= $nombre_columna." = ".$valor."' and ";
                }
                break;

            case 'no igual a':
                if ($nombre_columna == "m.fecha_muestreo") {
                    $query_principal .= $nombre_columna." > '".$valor." 23:59:59' and ".$nombre_columna." < '".$valor." 00:00:00' and ";
                } else {
                    $query_principal .= $nombre_columna." <> '".$valor."' and ";
                }
                break;
                
            case 'vacio':
                $query_principal .= $nombre_columna." IS NULL and ";
                break;

            case 'no vacio':
                $query_principal .= $nombre_columna." IS NOT NULL and ";
                break;

            case strtolower(trans('texto.mayor_que')):
                $query_principal .= $nombre_columna." > '".$valor." 00:00:00' and ";
                break;
            
            case strtolower(trans('texto.menor_que')):
                $query_principal .= $nombre_columna." < '".$valor." 23:59:59' and ";
                break;

            default:
                # code...
                break;
        }
        
        return $query_principal;
    }

    private function getExtensionFromContentType($contentType)
    {
        // Ejemplo: application/pdf -> pdf
        $parts = explode('/', $contentType);
        if (count($parts) === 2) {
            return $parts[1];
        }
        
        // Si no se puede determinar la extensión, se puede establecer un valor predeterminado o manejar el caso específico.
        return 'unknown';
    }
}
