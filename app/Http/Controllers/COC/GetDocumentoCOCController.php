<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CadenaCustodiaExport;

class GetDocumentoCOCController extends Controller
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
        $datos['cliente'] = "Cerro Verde";
        $datos['contacto'] = "Juan Perez";
        $datos['correo'] = "juan.perez@alsglobal.com";
        $datos['procedencia'] = "comedor";
        $datos['proyecto'] = "proyecto NN";
        $datos['numero_proceso'] = "1234/2021";
        $datos['numero_os'] = "-";
        $abcd = [1,2,3,4,5,5];
        $export = new CadenaCustodiaExport();
        $export->setData($datos,$abcd);
        return Excel::download($export, 'COC.xlsx');
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
