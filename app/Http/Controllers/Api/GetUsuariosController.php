<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetUsuariosController extends Controller
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
        $nombre = $request->nombre;
        $email = $request->email;
        $numero_fila = $request->rowPage;


        $usuarios = DB::table('users as u')->select('u.id', 'u.name', 'u.email', 'e.nombre_empresa', 'r.rol', 'u.activo')
                    ->leftJoin('rols as r', 'r.id', '=', 'u.id_rol')
                    ->leftJoin('empresas as e', 'e.id', '=', 'u.id_empresa');
                    

        if (isset($nombre) and $nombre != "") {
            $usuarios = $usuarios->where('u.name', 'like', '%'.$nombre.'%');
        }

        if (isset($email) and $email != "") {
            $usuarios = $usuarios->where('u.email', 'like', '%'.$email.'%');
        }

        if ($numero_fila) {
            $usuarios = $usuarios->paginate(intval($numero_fila));
        } else {
            $usuarios = $usuarios->paginate(25);
        }
        
        return $usuarios;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $usuarios = DB::table('users as u')
                    ->leftJoin('rols as r', 'r.id', '=', 'u.id_rol')
                    ->leftJoin('usuario_empresas as ue', 'ue.id_usuario', '=', 'u.id')
                    ->where('u.id', $id)
                    ->where('u.activo', 'S')
                    ->where('ue.activo','S')
                    ->get();
        
        foreach ($usuarios as $value) {
            $rpta['nombre'] = $value->name;
            $rpta['email'] = $value->email;
            $rpta['id_empresas'][] = $value->id_empresa;
            $rpta['id_rol'] = $value->id_rol;
            $rpta['idioma'] = $value->idioma;
            $rpta['data_campo'] = $value->data_campo;
            $rpta['ver_empresa_sol'] = $value->ver_empresa_sol;
            $rpta['ver_contacto_sol'] = $value->ver_contacto_sol;
            $rpta['ver_empresa_con'] = $value->ver_empresa_con;
            $rpta['ver_contacto_con'] = $value->ver_contacto_con;
        }

        return $rpta;
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
