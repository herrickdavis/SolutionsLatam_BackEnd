<?php

namespace App\Http\Controllers\ApiClientes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClickBotones;

class LoginController extends Controller
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
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {            
            $user = $request->user();
            //pendiente hacer log del login
            foreach ($user->tokens as $token) {
                //utilizar name movil para api para celular
                if ($token->name == "api") {
                    $token->delete();
                }
            }
            /* Analytic API*/
            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $user->id;
            $analytic_click->id_boton = 30;
            $analytic_click->save();

            $usuario["status"] = 0;
            $usuario["data"]['name'] = $user->name;
            $usuario["data"]['token'] = $request->user()->createToken('api', ['server:read'])->plainTextToken;
            $usuario["msg"] = null;
        } else {
            $usuario["status"] = -1;
            $usuario["msg"] = 'Unauthenticated';
            return response()->json($usuario, 401);
        }
        return $usuario;
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
