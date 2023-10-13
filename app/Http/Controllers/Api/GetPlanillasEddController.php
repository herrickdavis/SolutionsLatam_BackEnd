<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Edd;
use GuzzleHttp;

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
        $url = "http://api-lims.alslatam.com/api/getPlanillaEDDcontroller";
        $query_json['id_empresa'] = 183;
        $token = 'xvmkC508o2sxXrA7302NMSBJsD0XCtWunbSi1Mmk0OGBUItToS';

        $client = new GuzzleHttp\Client();
        $res = $client->request('POST', $url, [
            'json' => $query_json, 'headers' => ['Authorization' => 'Bearer '.$token]
        ]);

        $edd_externo = json_decode($res->getBody(), true);

        $edds = Edd::select('id','nombre_reporte')->get()->toArray();
        

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
