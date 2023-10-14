<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


//use App\Models\MotivoMuestras;
use App\Models\Empresas;
use App\Models\Procesos;
use App\Models\Matrices;
use App\Models\TipoMuestras; //1 update por muestra
use App\Models\Proyectos; //obligatorio
use App\Models\ProcesoProyectos; //Obligatorio
use App\Models\Estaciones; //obligatorio
use App\Models\Unidades; //obligatorio
use App\Models\Metodos; // se subira como array insertorignore ya un pequeño cambio ya se vuelve un update
use App\Models\Limites; // uno por muestra
use App\Models\LimiteParametros; // se actualiza frecuentemente, si es historico sube como array
use App\Models\LimiteTipoMuestras; //1 por muestra necesario
use App\Models\Parametros; // insertorignore no cambian los nombres
use App\Models\MuestraParametros; //obligatorio actualizar uno por uno, si es historico como array
use App\Models\MuestraMetodos; //borro lo asociado a la muestra luego ingreso por array
use App\Models\Muestras; //obligatorio
use App\Models\Laboratorios;
//use App\Models\Areas;
use App\Models\LogUpload;

use App\Models\User;
use App\Models\UsuarioEmpresas;
use App\Models\GrupoMuestras;
use App\Models\MuestraGrupoMuestra;
use App\Models\ProcesoMuestras;

use Throwable;

class SetMuestraController extends Controller
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
        ini_set('max_execution_time', 600); //3 minutes
        ini_set('memory_limit', '256M');

        $datetime = new \DateTime('NOW');
        $datetime = $datetime->format('Y-m-d H:i:s.u');
        $hash = md5($datetime);

        $data_muestras = $request->json()->all();
        $hubo_error = false;
        foreach ($data_muestras as $data) {
            try {
                $log_upload = new LogUpload;
                $log_upload->id_user = 1;
                $log_upload->ip = $request->ip();
                $log_upload->url = 'SetMuestra';
                $log_upload->hash = $hash;
                $log_upload->payload = json_encode($data);

                $data = (object) $data;
                $historico = $data->historico;
                $id_muestra = $data->cdamostra;
                $numero_muestra = $data->numero_muestra;
                $id_grupo = $data->id_grupo;
                $numero_grupo = $data->numero_grupo;
                $anho_grupo = $data->anho_grupo;
                $id_proceso = $data->id_proceso;
                $numero_proceso = $data->numero_proceso;
                $anho_proceso = $data->anho_proceso;
                $nombre_proceso = $data->nombre_proceso;
                $proceso_activo = $data->proceso_activo;
                $id_estado = $data->id_estado;
                $id_motivo_muestra = $data->id_motivo_muestra;
                $nombre_motivo_muestra = $data->nombre_motivo_muestra;
                $id_empresa_sol = $data->id_empresa_sol;
                $codigo_empresa_sol = $data->codigo_empresa_sol;
                $nombre_empresa_sol = $data->nombre_empresa_sol;
                $nombre_usuario_sol = $data->nombre_usuario_sol;
                $email_usuario_sol = $data->email_usuario_sol;
                $id_empresa_con = $data->id_empresa_con;
                $codigo_empresa_con = $data->codigo_empresa_con;
                $nombre_empresa_con = $data->nombre_empresa_con;
                $nombre_usuario_con = $data->nombre_usuario_con;
                $email_usuario_con = $data->email_usuario_con;
                $id_pais = $data->id_pais;
                $proyecto = $data->proyecto;
                if ($proyecto == null or $proyecto == "") {
                    $proyecto = "---";
                }
                $id_tipo_muestra = $data->id_tipo_muestra;
                $nombre_tipo_muestra = $data->tipo_muestra;
                $id_matriz = $data->id_matriz;
                $nombre_matriz = $data->matriz;
                $id_limite = $data->id_limite;
                $nombre_limite = $data->nombre_limite;
                $desc_limite = $data->desc_limite;
                $fecha_muestreo = $data->fecha_muestreo;
                $fecha_prevista_entrega = $data->fecha_entrega;
                $fecha_publicacion = $data->fecha_publicacion;
                $activo = $data->activo;
                //data para el log
                $log_upload->id_muestra = json_encode($id_muestra);
                $log_upload->id_grupo = json_encode($id_grupo);
                
                //estacion
                $estacion = [];
                $estacion['nombre_estacion'] = trim(str_replace("  ", " ", $data->estacion['nombre_estacion']));
                $estacion['longitud_este'] = trim($data->estacion['longitud_este']);
                $estacion['latitud_norte'] = trim($data->estacion['latitud_norte']);
                $estacion['zona'] = $data->estacion['zona'];
                $estacion['hemisferio'] = $data->estacion['hemisferio'];
                $estacion['procedencia'] = trim($data->estacion['procedencia']);
                ///////////// NORMALIZAMOS DATA ESTACION ////////////////////
                if (!is_numeric($estacion['longitud_este'])) {
                    $estacion['longitud_este'] = null;
                }
                if (!is_numeric($estacion['latitud_norte'])) {
                    $estacion['latitud_norte'] = null;
                }

                $estacion['zona'] = trim(strtoupper(str_replace(' ', '', $estacion['zona'])));
                if (strlen($estacion['zona']) > 3) {
                    $estacion['zona'] = null;
                } else {
                    if ($estacion['hemisferio'] == null) {
                        switch (substr($estacion['zona'], -1)) {
                        case 'N':
                            $estacion['hemisferio'] = 'N';
                            break;
                        case 'P':
                            $estacion['hemisferio'] = 'N';
                            break;
                        case 'Q':
                            $estacion['hemisferio'] = 'N';
                            break;
                        case 'R':
                            $estacion['hemisferio'] = 'N';
                            break;
                        case 'S':
                            $estacion['hemisferio'] = 'N';
                            break;
                        case 'T':
                            $estacion['hemisferio'] = 'N';
                            break;
                        default:
                            $estacion['hemisferio'] = 'S';
                            break;
                    }
                    }
                }
                /////////////////////////////////////////////////////////////

                //parametros
                $parametros = [];
                //otros parametros
                $parametros2 = [];
                //metodos
                $metodos = [];
                //muestra metodos
                $muestra_metodos = [];
                //areas
                $areas = [];
                //laboratorio
                $laboratorios = [];
                //limites
                $limite_parametros = [];

                $requet_parametros = $data->parametros;
                $parecer_muestra = 1;
                $data_muestra = "N";
                foreach ($requet_parametros as $parametro) {
                    $pre_parametros = [];
                    $pre_parametros['id_parametro'] = $parametro['cdvs'];
                    $pre_parametros['nombre_parametro'] = $parametro['nombre'];
                    $pre_parametros['valor'] = $parametro['valor'];
                    //para verificar si tiene o no data
                    if ($pre_parametros['valor'] != null) {
                        $data_muestra = 'S';
                    }
                
                    $pre_parametros['unidad'] = $parametro['unidad'];
                    $pre_parametros['cd_metodo'] = $parametro['cd_metodo'];
                    $pre_parametros['id_metodo'] = $parametro['id_metodo'];
                    $pre_parametros['version_metodo'] = $parametro['version_metodo'];
                    $pre_parametros['metodo'] = $parametro['metodo'];
                    $pre_parametros['desc_metodo'] = $parametro['desc_metodo'];
                    $pre_parametros['referencia_metodo'] = $parametro['referencia_metodo'];
                    $pre_parametros['es_acreditado'] = $parametro['metodo_acreditado'];
                    $pre_parametros['id_laboratorio'] = $parametro['id_laboratorio'];
                    $pre_parametros['nombre_laboratorio'] = $parametro['nombre_laboratorio'];
                    $pre_parametros['id_area'] = $parametro['id_area'];
                    $pre_parametros['nombre_area'] = $parametro['nombre_area'];
                    $pre_parametros['limite_maximo'] = $parametro['limite_maximo'];
                    $pre_parametros['limite_minimo'] = $parametro['limite_minimo'];
                    $pre_parametros['id_parecer'] = $parametro['id_parecer'];
                    if ($pre_parametros['id_parecer'] > $parecer_muestra) {
                        $parecer_muestra = $pre_parametros['id_parecer'];
                    }
                    array_push($parametros, $pre_parametros);

                    $pre_parametros = [];
                    $pre_parametros['id'] = $parametro['cdvs'];
                    $pre_parametros['nombre_parametro'] = $parametro['nombre'];
                    array_push($parametros2, $pre_parametros);

                    $pre_metodos = [];
                    $pre_metodos['id'] = $parametro['cd_metodo'];
                    $pre_metodos['idaux_metodo'] = $parametro['id_metodo'];
                    $pre_metodos['version_metodo'] = $parametro['version_metodo'];
                    $pre_metodos['nombre_metodo'] = $parametro['metodo'];
                    $pre_metodos['desc_metodo'] = $parametro['desc_metodo'];
                    $pre_metodos['referencia_metodo'] = $parametro['referencia_metodo'];
                    $pre_metodos['es_acreditado'] = $parametro['metodo_acreditado'];
                    $pre_metodos['activo'] = 'S';
                    if ($parametro['cd_metodo'] != null) {
                        array_push($metodos, $pre_metodos);
                    }

                    $pre_muestra_metodos = [];
                    $pre_muestra_metodos['id_muestra'] = $id_muestra;
                    $pre_muestra_metodos['cd_metodo'] = $parametro['cd_metodo'];
                    $pre_muestra_metodos['id_laboratorio'] = $parametro['id_laboratorio'];
                    if ($parametro['cd_metodo'] != null) {
                        array_push($muestra_metodos, $pre_muestra_metodos);
                    }

                    $pre_areas = [];
                    $pre_areas['id_area'] = $parametro['id_area'];
                    $pre_areas['nombre_area'] = $parametro['nombre_area'];
                    if ($parametro['id_area'] != null) {
                        array_push($areas, $pre_areas);
                    }

                    $pre_laboratorios = [];
                    $pre_laboratorios['id_laboratorio'] = $parametro['id_laboratorio'];
                    $pre_laboratorios['nombre_laboratorio'] = $parametro['nombre_laboratorio'];
                    $pre_laboratorios['id_area'] = $parametro['id_area'];
                    if ($parametro['id_laboratorio'] != null) {
                        array_push($laboratorios, $pre_laboratorios);
                    }

                    $pre_parametros = [];
                    $pre_parametros['id_limite'] = $id_limite;
                    $pre_parametros['id_parametro'] = $parametro['cdvs'];
                    $pre_parametros['maximo'] = $parametro['limite_maximo'];
                    $pre_parametros['minimo'] = $parametro['limite_minimo'];
                    if ($id_limite != null) {//solo ingreso limites cuando trae limite
                        array_push($limite_parametros, $pre_parametros);
                    }
                }
            
                $metodos = array_unique($metodos, SORT_REGULAR);
                $muestra_metodos = array_unique($muestra_metodos, SORT_REGULAR);
                $areas = array_unique($areas, SORT_REGULAR);
                $laboratorios = array_unique($laboratorios, SORT_REGULAR);
                $limite_parametros = array_unique($limite_parametros, SORT_REGULAR);

                /*$sql_motivo_muestra = MotivoMuestras::updateOrCreate(
                    ['id' => $id_motivo_muestra],
                    ['nombre_motivo_muestra' => $nombre_motivo_muestra]
                );*/
                Procesos::where('numero', $numero_proceso)
                            ->where('anho', $anho_proceso)
                            ->update(['activo' => 'N']);
                            
                $sql_empresa_sol = Procesos::updateOrCreate(
                    ['id' => $id_proceso],
                    ['numero' => $numero_proceso, 'anho' => $anho_proceso, 'nombre_proceso' => $nombre_proceso, 'activo' => $proceso_activo]
                );
                
                $sql_empresa_sol = Empresas::updateOrCreate(
                    ['id' => $id_empresa_sol],
                    ['nombre_empresa' => $nombre_empresa_sol, 'id_pais' => $id_pais, 'codigo_empresa' => $codigo_empresa_sol]
                );

                $sql_empresa_con = Empresas::updateOrCreate(
                    ['id' => $id_empresa_con],
                    ['nombre_empresa' => $nombre_empresa_con, 'id_pais' => $id_pais, 'codigo_empresa' => $codigo_empresa_con]
                );
                
                $sql_matriz = Matrices::updateOrCreate(
                    ['id' => $id_matriz],
                    ['nombre_matriz' => $nombre_matriz]
                );
                
                if ($historico != 'S') {
                    $sql_tipo_muestra = TipoMuestras::updateOrCreate(
                        ['id' => $id_tipo_muestra],
                        ['nombre_tipo_muestra' => $nombre_tipo_muestra]
                    );
                }

                $sql_proceso_proyecto = ProcesoProyectos::updateOrCreate(
                    ['id_proceso' => $id_proceso],
                    ['nombre_proyecto' => $proyecto]
                );

                $sql_proyecto = Proyectos::updateOrCreate(
                    ['nombre_proyecto' => $proyecto],
                    [
                        'nombre_proyecto' => $proyecto
                    ]
                );
        
                if ($estacion['latitud_norte'] != null and $estacion['longitud_este'] != null and $estacion['zona']) {
                    $sql_estacion = Estaciones ::updateOrCreate(
                        ['id_empresa_sol' => $id_empresa_sol ,'nombre_estacion' => $estacion['nombre_estacion']],
                        [
                        'id_empresa_con' => $id_empresa_con,
                        'latitud_n' => $estacion['latitud_norte'],
                        'longitud_e' => $estacion['longitud_este'],
                        'zona' => $estacion['zona'],
                        'hemisferio' => $estacion['hemisferio'],
                        'procedencia' => $estacion['procedencia'],
                        'activo' => 'S'
                    ]
                    );
                } else {
                    $sql_estacion = Estaciones ::firstOrCreate(
                        ['id_empresa_sol' => $id_empresa_sol ,'nombre_estacion' => $estacion['nombre_estacion']],
                        [
                        'id_empresa_con' => $id_empresa_con,
                        'latitud_n' => $estacion['latitud_norte'],
                        'longitud_e' => $estacion['longitud_este'],
                        'zona' => $estacion['zona'],
                        'hemisferio' => $estacion['hemisferio'],
                        'procedencia' => $estacion['procedencia'],
                        'activo' => 'S'
                    ]
                    );
                }

                if ($id_limite != null) { //solo ingreso limites cuando trae limites
                    $sql_limite = Limites::updateOrCreate(
                        ['id' => $id_limite],
                        [
                        'nombre_limite' => $nombre_limite,
                        'desc_limite' => $desc_limite,
                        'activo' => 'S',
                    ]
                    );

                
                    $sql_limite_muestras = LimiteTipoMuestras::updateOrCreate(
                        ['id_limite' => $id_limite, 'id_tipo_muestra' => $id_tipo_muestra],
                        []
                    );
                }

                if ($email_usuario_con == null) {
                    $email_usuario_con = $id_empresa_con.'@alsglobal.com';
                    $nombre_usuario_con = 'ALS GLOBAL';
                }

                $sql_usuario_con = User::firstOrCreate(
                    ['email' => $email_usuario_con],
                    [
                'name' => $nombre_usuario_con,
                'password' => Hash::make('12345678'),
                //'password' => Hash::make(Str::random(10)),
                'id_empresa' => $id_empresa_con,
                'id_rol' => 4,
            ]
                );

                $sql_usuario_emp_con = UsuarioEmpresas::firstOrCreate(
                    [
                    'id_usuario' => $sql_usuario_con->id,
                    'id_empresa' => $id_empresa_con,
                ],
                    []
                );

                if ($email_usuario_sol == null) {
                    $email_usuario_sol = $id_empresa_sol.'@alsglobal.com';
                    $nombre_usuario_sol = 'ALS GLOBAL';
                }

                $sql_usuario_sol = User::firstOrCreate(
                    ['email' => $email_usuario_sol],
                    [
                'name' => $nombre_usuario_sol,
                'password' => Hash::make('12345678'),
                //'password' => Hash::make(Str::random(10)),
                'id_empresa' => $id_empresa_sol,
                'id_rol' => 4,
            ]
                );

                $sql_usuario_emp_sol = UsuarioEmpresas::firstOrCreate(
                    [
                    'id_usuario' => $sql_usuario_sol->id,
                    'id_empresa' => $id_empresa_sol,
                ],
                    []
                );

                $muestra_con_informe = Muestras::where('id', '=', $id_muestra)->first();
                if ($muestra_con_informe != null) {
                    if ($muestra_con_informe->id_certificado != null) {
                        $id_estado = 4;
                    }
                }
                
                $sql_muestra = Muestras::updateOrCreate(
                    ['id' => $id_muestra],
                    [
                    'numero_muestra' => $numero_muestra,
                    //'id_grupo' => $id_grupo,
                    //'numero_grupo' => $numero_grupo,
                    //'id_proceso' => $id_proceso,
                    'id_estado' => $id_estado,
                    'id_parecer' => $parecer_muestra,
                    'con_data' => $data_muestra,
                    'id_motivo_muestra' => $id_motivo_muestra,
                    'id_empresa_con' => $id_empresa_con,
                    'id_user_con' => $sql_usuario_con->id,
                    'id_empresa_sol' => $id_empresa_sol,
                    'id_user_sol' => $sql_usuario_sol->id,
                    'id_estacion' => $sql_estacion->id,
                    'id_proyecto' => $sql_proyecto->id,
                    'id_proceso_proyecto' => $sql_proceso_proyecto->id,
                    'id_tipo_muestra' => $id_tipo_muestra,
                    'id_matriz' => $id_matriz,
                    'id_limite' => $id_limite,
                    'fecha_muestreo' => ($fecha_muestreo != null) ? date('Y-m-d H:i:s', strtotime($fecha_muestreo)) : null,
                    'fecha_prevista_entrega' => ($fecha_prevista_entrega != null) ? date('Y-m-d H:i:s', strtotime($fecha_prevista_entrega)) : null,
                    'fecha_publicacion' => ($fecha_publicacion != null) ? date('Y-m-d H:i:s', strtotime($fecha_publicacion)) : null,
                    'activo' => $activo
                ]
                );

                $sql_grupo = GrupoMuestras::updateOrCreate(
                    ['id' => $id_grupo],
                    [
                    'numero_grupo' => $numero_grupo,
                    'anho_grupo' => $anho_grupo
                ]
                );

                $sql_muestra_grupo_muestra = MuestraGrupoMuestra::updateOrCreate(
                    ['id_grupo_muestra' => $id_grupo, 'id_muestra' => $id_muestra],
                    []
                );

                $sql_proceso_muestra = ProcesoMuestras::updateOrCreate(
                    ['id_proceso' => $id_proceso, 'id_muestra' => $id_muestra],
                    []
                );

                /*foreach ($areas as $area) {
                    $sql_area = Areas::updateOrCreate(
                        ['id' => $area['id_area']],
                        [
                            'nombre_area' => $area['nombre_area'],
                        ]
                    );
                }*/

                foreach ($laboratorios as $laboratorio) {
                    $sql_laboratorio = Laboratorios::updateOrCreate(
                        ['id' => $laboratorio['id_laboratorio']],
                        [
                            'nombre_laboratorio' => $laboratorio['nombre_laboratorio'],
                            'id_area' => $laboratorio['id_area'],
                        ]
                    );
                }

                $sql_metodo = Metodos::insertOrIgnore($metodos);

                /*foreach ($metodos as $metodo) {
                    $sql_metodo = Metodos::updateOrCreate(
                        ['id' => $metodo['cd_metodo']],
                        [
                            'idaux_metodo' => $metodo['id_metodo'],
                            'version_metodo' => $metodo['version_metodo'],
                            'nombre_metodo' => $metodo['metodo'],
                            'desc_metodo' => $metodo['desc_metodo'],
                            'referencia_metodo' => $metodo['referencia_metodo'],
                            'es_acreditado' => $metodo['es_acreditado'],
                        ]
                    );
                }*/

                //elimino muestrametodos para el codigo de la muestra luego añado
                MuestraMetodos::where('id_muestra', '=', $id_muestra)->delete();
                foreach ($muestra_metodos as $muestra_metodo) {
                    $sql_muestra_metodos = MuestraMetodos::updateOrCreate(
                        ['id_muestra' => $id_muestra, 'id_metodo' => $muestra_metodo['cd_metodo']],
                        [
                        'id_laboratorio' => $muestra_metodo['id_laboratorio']
                    ]
                    );
                }

                Parametros::insertOrIgnore($parametros2);

                if ($historico == "S") {
                    if ($id_limite != null) {
                        LimiteParametros::upsert(
                            $limite_parametros,
                            ['id_limite', 'id_parametro'],
                            ['maximo', 'minimo']
                        );
                        
                    }

                    $parametros3 = [];
                    $requet_parametros = $data->parametros;
                    foreach ($requet_parametros as $parametro) {
                        $pre_parametros = [];
                        $pre_parametros['id_muestra'] = $id_muestra;
                        $pre_parametros['id_parametro'] = $parametro['cdvs'];
                        $pre_parametros['id_metodo'] = $parametro['cd_metodo'];
                        $pre_parametros['valor'] = $parametro['valor'];
                        if ($parametro['unidad'] != null) {
                            $sql_unidad = Unidades::updateOrCreate(
                                ['unidad' => $parametro['unidad']],
                                ['unidad' => $parametro['unidad']]
                            );
                            $pre_parametros['id_unidad'] = $sql_unidad->id;
                        } else {
                            $pre_parametros['id_unidad'] = null;
                        }
                    
                        $pre_parametros['id_parecer'] = $parametro['id_parecer'];
                        array_push($parametros3, $pre_parametros);
                    }
                    MuestraParametros::insertOrIgnore($parametros3);
                } else {
                    foreach ($parametros as $parametro) {
                        if ($id_limite != null and $historico != 'S') {//solo ingreso limites cuando trae limite
                            $sql_limite_parametros = LimiteParametros::updateOrCreate(
                                ['id_limite' => $id_limite, 'id_parametro' => $parametro['id_parametro']],
                                [
                                'maximo' => $parametro['limite_maximo'],
                                'minimo' => $parametro['limite_minimo'],
                            ]
                            );
                        }

                        if ($parametro['unidad'] != null) {
                            $sql_unidad = Unidades::updateOrCreate(
                                ['unidad' => $parametro['unidad']],
                                ['unidad' => $parametro['unidad']]
                            );
                            $id_unidad = $sql_unidad->id;
                        } else {
                            $id_unidad = null;
                        }


                        if($id_estado == 3 or $id_estado == 4) {
                            if (is_null($parametro['valor']) || trim($parametro['valor']) === "") {
                                //Eliminados el parametro en caso llegue nulo siendo la muestra finalizada o con informe
                                MuestraParametros::where('id_muestra', $id_muestra)
                                                    ->where('id_parametro', $parametro['id_parametro'])
                                                    ->delete();
                                continue;
                            }
                        } 
                        
                        $sql_parametro = MuestraParametros::updateOrCreate(
                            ['id_muestra' => $id_muestra, 'id_parametro' => $parametro['id_parametro']],
                            [
                            'id_metodo' => $parametro['cd_metodo'],
                            'valor' => $parametro['valor'],
                            'id_unidad' => $id_unidad,
                            'id_parecer' => $parametro['id_parecer']
                            ]
                        );
                    }
                }
                
                //guardare solo los que provoquen algun error para ahorrar espacio DB
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
