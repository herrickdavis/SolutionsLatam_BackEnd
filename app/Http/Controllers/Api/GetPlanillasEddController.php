<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Edd;
use GuzzleHttp;
use Throwable;

class GetPlanillasEddController extends Controller
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
        $edd_externo = [];
        $usuario = $request->user();
        try {
            $url = "http://api-lims.alslatam.com/api/getPlanillaEDDcontroller";
            $query_json['id_empresa'] = $usuario->id_empresa;
            $token = 'xvmkC508o2sxXrA7302NMSBJsD0XCtWunbSi1Mmk0OGBUItToS';

            $client = new GuzzleHttp\Client();
            $res = $client->request('POST', $url, [
                'json' => $query_json, 'headers' => ['Authorization' => 'Bearer '.$token]
            ]);

            $edd_externo = json_decode($res->getBody(), true);
            if(array_key_exists('message',$edd_externo)) {
                $edd_externo = [];
            }
        } catch(Throwable $e) {
            report($e);
        }
        

        $edds = Edd::select('id','nombre_reporte')->where('user_id',$usuario->id)->get()->toArray();
        

        $convertedArray1 = array_map(function ($item) {
            return [
                "id" => $item["ID"]."E",
                "nombre_reporte" => $item["NOMBRE_REPORTE"]
            ];
        }, $edd_externo);
        
        $result = array_merge($convertedArray1, $edds);

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $edd = Edd::where('id','=',$id)->first();
        return $edd;
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
