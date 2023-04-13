<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;

class GetDataCOCController extends Controller
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
        $fecha = $request->fecha;
        $tipo_muestra = $request->tipo_muestra;
        if (!isset($fecha)) {
            return [];
        }

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://onlinedata.alslatam.com/wsmylims/public/']);
        
        if (isset($page)) {
            if (isset($tipo_muestra)) {
                $response = $client->request('POST', 'muestras_coc', ['query' => ['page' => $page],'json' => ['fecha' => $fecha, 'tipo_muestra' => $tipo_muestra]]);
            } else {
                $response = $client->request('POST', 'muestras_coc', ['query' => ['page' => $page],'json' => ['fecha' => $fecha]]);
            }
        } else {
            if (isset($tipo_muestra)) {
                $response = $client->request('POST', 'muestras_coc', ['json' => ['fecha' => $fecha, 'tipo_muestra' => $tipo_muestra]]);
            } else {
                $response = $client->request('POST', 'muestras_coc', ['json' => ['fecha' => $fecha]]);
            }
        }

        $respuesta = json_decode($response->getBody()->getContents(), true);
        $respuesta['muestras']['next_page_url'] = str_replace('http://onlinedata.alslatam.com/wsmylims/public/muestras_coc', 'https://api-solutions.alslatam.com/api/GetDataCOC', $respuesta['muestras']['next_page_url']);
        $respuesta['muestras']['prev_page_url'] = str_replace('http://onlinedata.alslatam.com/wsmylims/public/muestras_coc', 'https://api-solutions.alslatam.com/api/GetDataCOC', $respuesta['muestras']['prev_page_url']);
        
        return $respuesta;//['muestras']['next_page_url'];
    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $collection = new Collection($items);

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $currentPageResults = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        //return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return new LengthAwarePaginator($currentPageResults, $items->count(), $perPage, $page, $options);
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
