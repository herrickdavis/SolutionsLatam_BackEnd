<?php
use Illuminate\Support\Facades\DB;

function filtroMuestrasQuery($query,$user) {

    $id_empresas = getIdEmpresas($user);

    $ver_empresa_sol = $user->ver_empresa_sol;
    $ver_contacto_sol = $user->ver_contacto_sol;
    $ver_empresa_con = $user->ver_empresa_con;
    $ver_contacto_con = $user->ver_contacto_con;

    if ($ver_empresa_sol == 'S' and $ver_empresa_con == 'S') {
        $query = $query->where(function($query) use($id_empresas){
            $query->whereIn('m.id_empresa_sol', $id_empresas)
            ->orWhereIn('m.id_empresa_con', $id_empresas);
        });
    } elseif ($ver_empresa_sol == 'S') {
        $query = $query->whereIn('m.id_empresa_sol', $id_empresas);
    } elseif ($ver_empresa_con == 'S') {
        $query = $query->whereIn('m.id_empresa_con', $id_empresas);
    } elseif ($ver_contacto_sol == 'S') {
        $query = $query->where('m.id_user_sol', $user->id);
    } elseif ($ver_contacto_con == 'S') {
        $query = $query->where('m.id_user_con', $user->id);
    }

    return $query;
}

function getIdEmpresas($user) {
    $sql_id_empresas = DB::table('usuario_empresas as ue')
                        ->where('id_usuario', $user->id)
                        ->where('activo','S')->get();
    $id_empresas = [];
    foreach ($sql_id_empresas as $valor) {
        array_push($id_empresas, $valor->id_empresa);
    }

    if (count($id_empresas) == 0 or $user->id_rol < 4) {
        $id_empresas = [];
        array_push($id_empresas, $user->id_empresa);
    }

    return $id_empresas;
}
?>