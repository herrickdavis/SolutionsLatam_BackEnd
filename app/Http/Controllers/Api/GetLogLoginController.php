<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetLogLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $login = DB::table('log_logins as ll')
                    ->select(DB::raw(
                        "ll.id AS id,
                        ll.id_user as id_user,
                        u.name as name,
                        u.email as email,
                        u.id_rol as rol,
                        r.rol,
                        u.id_empresa as id_empresa,
                        e.nombre_empresa as empresa,
                        ll.created_at as fecha
                        "
                    ))
                    ->leftjoin('users AS u', 'u.id', '=', 'll.id_user')
                    ->leftjoin('empresas AS e', 'e.id', '=', 'u.id_empresa')
                    ->leftjoin('rols AS r', 'r.id', '=', 'u.id_rol')
                    ->get();

        return($login);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $login = DB::table('log_logins as ll')
                    ->select(DB::raw(
                        "ll.id AS id,
                        ll.id_user as id_user,
                        u.name as name,
                        u.email as email,
                        u.id_rol as rol,
                        r.rol,
                        u.id_empresa as id_empresa,
                        e.nombre_empresa as empresa,
                        ll.created_at as fecha
                        "
                    ))
                    ->leftjoin('users AS u', 'u.id', '=', 'll.id_user')
                    ->leftjoin('empresas AS e', 'e.id', '=', 'u.id_empresa')
                    ->leftjoin('rols AS r', 'r.id', '=', 'u.id_rol')
                    ->get();

        return($login);
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
