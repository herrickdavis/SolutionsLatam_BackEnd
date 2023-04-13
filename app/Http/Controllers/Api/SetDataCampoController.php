<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DataCampo;

use Throwable;

class SetDataCampoController extends Controller
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
        $idauxempresa = $request[0]['id_empresa_sol'];
        try {
            if ($idauxempresa != null and is_numeric($idauxempresa)) {
                DataCampo::where('id_empresa_sol', '=', $idauxempresa)->delete();
                DataCampo::insert($request->toArray());
            }
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
