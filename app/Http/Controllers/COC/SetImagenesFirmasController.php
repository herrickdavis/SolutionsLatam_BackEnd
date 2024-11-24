<?php

namespace App\Http\Controllers\COC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetImagenesFirmasController extends Controller
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
        // Validar que el archivo sea una imagen
        $request->validate([
            'image' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048', // Máximo 2 MB
        ]);

        // Obtener el archivo de la solicitud
        $file = $request->file('image');

        // Usar el nombre original del archivo
        $filename = $file->getClientOriginalName();

        // Guardar el archivo en storage/app/img_firmas con el nombre original
        $path = $file->storeAs('img_firmas', $filename);

        // Retornar respuesta
        return response()->json([
            'message' => 'Imagen subida con éxito',
            'path' => $path,
        ]);
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
