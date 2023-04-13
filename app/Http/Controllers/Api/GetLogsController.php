<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Throwable;

class GetLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (isset($_GET['orden'])) {
            $orden = $_GET['orden'];
        } else {
            $orden = 'desc';
        }
        
        $log = DB::table('log_uploads')->select('id', 'url', 'id_muestra', 'payload', 'exception', 'created_at')->orderBy('created_at', $orden)
                ->whereNotNull('exception')
                ->where('estado', 'P')->paginate(15);

        return $log;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id_muestra = $request->id_muestra;
        $exception = $request->exception;
        $log = DB::table('log_uploads')->select('id', 'url', 'id_muestra', 'payload', 'exception', 'created_at')->where('id_muestra', $id_muestra)->orderBy('created_at', 'desc');
        if ($exception) {
            $log = $log->whereNotNull('exception');
        }

        $log = $log->get();

        return $log;
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
        try {
            $log = DB::table('log_uploads')->where('id_muestra', $id)
                ->update(['estado' => 'C']);
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
            $rpta['mensaje'] = "Ok";
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
        //
    }
}
