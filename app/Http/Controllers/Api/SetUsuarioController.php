<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\UsuarioEmpresas;

use Illuminate\Support\Facades\DB;

use Throwable;

class SetUsuarioController extends Controller
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
        $hubo_error = false;

        $usuario = $request->user();
        if ($usuario->rol > 2) {
            $rpta['error'] = "error";
            $rpta['mensaje'] = "No tiene los permisos necesarios para crear usuarios";
            return $rpta;
        }

        try {
            $update = $request->update;
            $nombre = $request->nombre;
            $email = $request->email;
            $password = $request->password;
            $id_empresas = $request->id_empresas;
            $id_rol = $request->id_rol;

            //configuraciones
            $ver_empresa_sol = $request->ver_empresa_solicitante;
            $ver_contacto_sol = $request->ver_contacto_solicitante;
            $ver_empresa_con = $request->ver_empresa_contratante;
            $ver_contacto_con = $request->ver_contacto_contratante;
            $idioma = $request->idioma;
            $data_campo = $request->data_campo;
            $telemetria = $request->telemetria;
            $documentos = $request->documentos;
            $id_region = $request->region;
            
            $user = new User();
            $user->name = $nombre;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->id_empresa = $id_empresas[0];
            $user->id_rol = $id_rol;
            $user->ver_empresa_sol = $ver_empresa_sol;
            $user->ver_contacto_sol = $ver_contacto_sol;
            $user->ver_empresa_con = $ver_empresa_con;
            $user->ver_contacto_con = $ver_contacto_con;
            $user->idioma = $idioma;
            $user->data_campo = $data_campo;
            $user->telemetria = $telemetria;
            $user->descargar_documento = $documentos;
            $user->id_region = $id_region;
            $user->save();

            foreach ($id_empresas as $id_empresa) {
                $user_empresa = new UsuarioEmpresas();
                $user_empresa->id_usuario = $user->id;
                $user_empresa->id_empresa = $id_empresa;
                $user_empresa->save();
            }
        } catch (Throwable $e) {
            report($e);
            $hubo_error = true;
            $mensaje = $e->getMessage();
        }

        if ($hubo_error) {
            $rpta['error'] = "error";
            $rpta['mensaje'] = $mensaje;

            return $rpta;
        } else {
            $rpta['success'] = "success";
            $rpta['mensaje'] = "Se creo el usuario correctamente";
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
        $hubo_error = false;
        $usuario = $request->user();
        if ($usuario->rol > 2) {
            $rpta['error'] = "error";
            $rpta['mensaje'] = "No tiene los permisos necesarios para crear usuarios";
            return $rpta;
        }
        \Log::info($request);
        try {
            $update = $request->update;
            $nombre = $request->nombre;
            $email = $request->email;
            $password = $request->password;
            $id_empresas = $request->id_empresas;
            $id_rol = $request->id_rol;

            //configuraciones
            $ver_empresa_sol = $request->ver_empresa_sol;
            $ver_contacto_sol = $request->ver_contacto_sol;
            $ver_empresa_con = $request->ver_empresa_con;
            $ver_contacto_con = $request->ver_contacto_con;
            $idioma = $request->idioma;
            $data_campo = $request->data_campo;
            $telemetria = $request->telemetria;
            $documentos = $request->documentos;
            $id_region = $request->region;

            $user = User::where('email', '=', $email)->first();
            if (isset($user)) {
                $user->name = $nombre;
                $user->email = $email;
                if($password != "") {
                    $user->password = Hash::make($password);
                }                
                $user->id_empresa = $id_empresas[0];
                $user->id_rol = $id_rol;
                $user->ver_empresa_sol = $ver_empresa_sol;
                $user->ver_contacto_sol = $ver_contacto_sol;
                $user->ver_empresa_con = $ver_empresa_con;
                $user->ver_contacto_con = $ver_contacto_con;
                $user->idioma = $idioma;
                $user->data_campo = $data_campo;
                $user->telemetria = $telemetria;
                $user->descargar_documento = $documentos;
                $user->id_region = $id_region;
                $user->save();
                
                //Desactivo todos los usuarios empresa para ir agregando o activando los que se utilicen
                DB::table('usuario_empresas')->where('id_usuario', $user->id)->update(['activo' => 'N']);
                foreach ($id_empresas as $id_empresa) {
                    DB::table('usuario_empresas')
                            ->updateOrInsert(
                                ['id_usuario' => $user->id, 'id_empresa' => $id_empresa],
                                ['activo' => 'S']
                            );
                }
            }
        } catch (Throwable $e) {
            report($e);
            $hubo_error = true;
            $mensaje = $e->getMessage();
        }

        if ($hubo_error) {
            $rpta['error'] = "error";
            $rpta['mensaje'] = $mensaje;

            return $rpta;
        } else {
            $rpta['success'] = "success";
            $rpta['mensaje'] = "Se actualizo correctamente el usuario";
            return $rpta;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $hubo_error = false;
        try {
            $update = User::find($id);
            if ($update->activo == 'S') {
                $update->activo = 'N';
            } else {
                $update->activo = 'S';
            }
            
            $update->save();
        } catch (Throwable $e) {
            report($e);
            $hubo_error = true;
            $mensaje = $e->getMessage();
        }

        if ($hubo_error) {
            $rpta['error'] = "error";
            $rpta['mensaje'] = $mensaje;

            return $rpta;
        } else {
            $rpta['success'] = "success";
            $rpta['mensaje'] = "Se actualizo correctamente el usuario";
            return $rpta;
        }
    }
}
