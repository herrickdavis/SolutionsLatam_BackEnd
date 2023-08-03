<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LogLogins;
use Illuminate\Support\Facades\Cache;

class GetLoginController extends Controller
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
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $usuario['nombre'] = $request->user()->name;
            $usuario['email'] = $request->user()->email;
            $usuario['idioma'] = $request->user()->idioma;
            $usuario['data_campo'] = $request->user()->data_campo;
            $usuario['rol'] = $request->user()->id_rol;            

            $user = $request->user();
            //inserto el login en los log
            $log_login = new LogLogins;
            $log_login->id_user = $user->id;
            $log_login->ip = $request->ip();
            $log_login->save();

            $sql_id_empresas = DB::table('usuario_empresas as ue')
                        ->where('id_usuario', $user->id)->get();
            $id_empresas = [];
            foreach ($sql_id_empresas as $valor) {
                array_push($this->idauxempresas, $valor->id_empresa);
            }

            if (count($this->idauxempresas) == 0) {
                array_push($this->idauxempresas, $request->user()->id_empresa);
            }            
            //grupo 999 and out grupo 998
            $id_empresas = getIdEmpresas($user);
            $cacheKey = 'matrices'.$user->ver_empresa_sol.'_'.$user->ver_contacto_sol.'_'.$user->ver_empresa_con.'_'.$user->ver_contacto_con.'_'.implode("_",$id_empresas);

            $sql_matrices = Cache::get($cacheKey);
            
            if (!$sql_matrices) {
                $query = DB::table('muestras AS m')
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
                $query = filtroMuestrasQuery($query,$user);
                $sql_matrices = $query->get();
                Cache::put($cacheKey, $sql_matrices, 21600);
            }

            $usuario['menu'] = $sql_matrices;

            foreach ($user->tokens as $token) {
                //utilizar name movil para api para celular
                if ($token->name == "web") {
                    $token->delete();
                }
            }
            if(isset($request->test)) {
                if($request->test) {
                    $usuario['token'] = $request->user()->createToken('test', ['server:read'])->plainTextToken;
                }
            } else {
                $usuario['token'] = $request->user()->createToken('web', ['server:read'])->plainTextToken;
            }
            
        } else {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        return $usuario;
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
