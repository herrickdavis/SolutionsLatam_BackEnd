<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CadenaCustodiaExport;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

class GetDocumentoCOCController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $excelFile = storage_path('app/public/Book1.xlsx');
        $pdfFile = storage_path('app/public/out.pdf');

        $spreadsheet = IOFactory::load($excelFile);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
    $writer->save($pdfFile);

    return response()->download($pdfFile);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Leemos Datos
        $id_cadena = 1;
        
        //Terminamos de leer datos
        $cadenas = DB::table("cadenas as c")
            ->where('c.id', $id_cadena)
            ->first();

        $parametros_laboratorio = DB::table("cadena_laboratorio_parametros as lp")
            ->where('lp.id_cadena', $id_cadena)
            ->get();

        $parametros_in_situ = DB::table("cadena_in_situ_parametros as ip")
            ->where('ip.id_cadena', $id_cadena)
            ->get();
        
        $export = new CadenaCustodiaExport();
        $export->setData($cadenas, $parametros_laboratorio, $parametros_laboratorio);
        
        return $export->export();
        //
        //return Excel::download($export, 'COC.xlsx');
        //return $users;
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
