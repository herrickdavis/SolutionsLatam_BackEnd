<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClickBotones;
use Throwable;

class GetReporteEstacionesController extends Controller
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
        try {
            $usuario = $request->user();
            $analytic_click = new ClickBotones;
            $analytic_click->id_user = $usuario->id;
            $analytic_click->id_boton = 28;
            $analytic_click->save();

            $id_tipo_muestra = str_replace("TA", "", $request->id_tipo_muestra);
            $id_parametro = [];
            if (substr($request->id_parametro, 0, 1) == "G") {
                $parametros = DB::table('grupo_parametros as gp')->select(
                    "pgp.id_parametro"
                )                
                ->leftjoin('parametro_grupo_parametros as pgp', 'pgp.id_grupo_parametro', '=', 'gp.id')
                ->where('gp.id','=',str_replace("G","",$request->id_parametro))
                ->get();
                foreach($parametros as $parametro) {
                    array_push($id_parametro,$parametro->id_parametro);
                }
            } else {
                array_push($id_parametro,str_replace("P", "", $request->id_parametro));
            }
            //dd($id_parametro);
            $estaciones = $request->id_estaciones;

            $data = [];
            foreach ($estaciones as $estacion) {
                $id_estacion = str_replace("E", "", $estacion);
                $sql_reporte_estaciones = DB::table('muestras as m')
                                ->select(DB::raw(
                                    "e.nombre_estacion as nombre_estacion,
                                    m.numero_muestra as numero_muestra,
                                    DATE_FORMAT(m.fecha_muestreo,'%d-%m-%Y') as fecha_muestreo,
                                    mp.valor as valor,
                                    un.unidad as unidad,
                                    e.latitud_n as latitud_n,
                                    e.longitud_e as longitud_e,
                                    e.zona as zona,
                                    e.hemisferio as hemisferio,
                                    CASE
                                    WHEN e.latitud_n is not null and e.longitud_e is not null and e.zona is not null then 'Si'
                                    ELSE 'No' END as punto_mapa
                                    "
                                ))
                                ->leftjoin('muestra_parametros as mp', 'mp.id_muestra', '=', 'm.id')
                                ->leftjoin('unidades as un', 'un.id', '=', 'mp.id_unidad')
                                ->leftjoin('estaciones as e', 'e.id', '=', 'm.id_estacion')
                                //->where('m.id_empresa_sol', '=', $idauxempresa)
                                ->where('m.activo', '=', 'S')
                                ->where('m.id_estado', '>=', 3)
                                ->where('m.id_tipo_muestra', '=', $id_tipo_muestra)
                                ->whereIn('mp.id_parametro', $id_parametro)
                                ->where('e.id', '=', $id_estacion)
                                ->orderBy('m.fecha_muestreo', 'DESC');                

                $sql_reporte_estaciones = filtroMuestrasQuery($sql_reporte_estaciones,$usuario);
                $sql_reporte_estaciones = $sql_reporte_estaciones->first();


                $pre_data['estacion'] = $sql_reporte_estaciones->nombre_estacion;
                $pre_data['muestra'] = $sql_reporte_estaciones->numero_muestra;
                $pre_data['fecha_muestreo'] = $sql_reporte_estaciones->fecha_muestreo;
                $pre_data['valor'] = str_replace(",", ".", $sql_reporte_estaciones->valor);
                $pre_data['unidad'] = str_replace(",", ".", $sql_reporte_estaciones->unidad);
                $pre_data['punto_mapa'] = $sql_reporte_estaciones->punto_mapa;
                $longitud = $sql_reporte_estaciones->longitud_e;
                $latitud = $sql_reporte_estaciones->latitud_n;
                $zona = $sql_reporte_estaciones->zona;
                $hemisferio = ($sql_reporte_estaciones->hemisferio == 'S') ? false:true;
                $pre_coordenadas = json_decode($this->utm2ll($longitud, $latitud, substr($zona, 0, 2), $hemisferio));
                if ($pre_coordenadas->success == true) {
                    $pre_data['latitud'] = "".$pre_coordenadas->attr->lat."";
                    $pre_data['longitud'] = "".$pre_coordenadas->attr->lon."";
                } else {
                    $pre_data['latitud'] = null;
                    $pre_data['longitud'] = null;
                }
                array_push($data, $pre_data);
            }
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return $data;
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

    //conversor UTM to
    public function utm2ll($x, $y, $zone, $aboveEquator)
    {
        if (!is_numeric($x) or !is_numeric($y) or !is_numeric($zone)) {
            return json_encode(array('success'=>false,'msg'=>"Wrong input parameters"));
        }
        $southhemi = false;
        if ($aboveEquator!=true) {
            $southhemi = true;
        }
        $latlon = $this->UTMXYToLatLon($x, $y, $zone, $southhemi);
        return json_encode(array('success'=>true,'attr'=>array('lat'=>$this->radian2degree($latlon[0]),'lon'=>$this->radian2degree($latlon[1]))));
    }
    public function ll2utm($lat, $lon)
    {
        if (!is_numeric($lon)) {
            return json_encode(array('success'=>false,'msg'=>"Wrong longitude value"));
        }
        if ($lon<-180.0 or $lon>=180.0) {
            return json_encode(array('success'=>false,'msg'=>"The longitude is out of range"));
        }
        if (!is_numeric($lat)) {
            return json_encode(array('success'=>false,'msg'=>"Wrong latitude value"));
        }
        if ($lat<-90.0 or $lat>90.0) {
            return json_encode(array('success'=>false,'msg'=>"The longitude is out of range"));
        }
        $zone = floor(($lon + 180.0) / 6) + 1;
        //compute values
        $result = $this->LatLonToUTMXY($this->degree2radian($lat), $this->degree2radian($lon), $zone);
        $aboveEquator = false;
        if ($lat >0) {
            $aboveEquator = true;
        }
        return json_encode(array('success'=>true,'attr'=>array('x'=>$result[0],'y'=>$result[1],'zone'=>$zone,'aboveEquator'=>$aboveEquator)));
    }

    public function radian2degree($rad)
    {
        $pi = 3.14159265358979;
        return ($rad / $pi * 180.0);
    }

    public function degree2radian($deg)
    {
        $pi = 3.14159265358979;
        return ($deg/180.0*$pi);
    }

    public function UTMCentralMeridian($zone)
    {
        $cmeridian = $this->degree2radian(-183.0 + ($zone * 6.0));
        return $cmeridian;
    }
    public function LatLonToUTMXY($lat, $lon, $zone)
    {
        $xy = $this->MapLatLonToXY($lat, $lon, $this->UTMCentralMeridian($zone));
        /* Adjust easting and northing for UTM system. */
        $UTMScaleFactor = 0.9996;
        $xy[0] = $xy[0] * $UTMScaleFactor + 500000.0;
        $xy[1] = $xy[1] * $UTMScaleFactor;
        if ($xy[1] < 0.0) {
            $xy[1] = $xy[1] + 10000000.0;
        }
        return $xy;
    }
    public function UTMXYToLatLon($x, $y, $zone, $southhemi)
    {
        $latlon = array();
        $UTMScaleFactor = 0.9996;
        $x -= 500000.0;
        $x /= $UTMScaleFactor;
        /* If in southern hemisphere, adjust y accordingly. */
        if ($southhemi) {
            $y -= 10000000.0;
        }
        $y /= $UTMScaleFactor;
        $cmeridian = $this->UTMCentralMeridian($zone);
        $latlon = $this->MapXYToLatLon($x, $y, $cmeridian);
        return $latlon;
    }
    public function MapXYToLatLon($x, $y, $lambda0)
    {
        $philambda = array();
        $sm_b = 6356752.314;
        $sm_a = 6378137.0;
        $UTMScaleFactor = 0.9996;
        $sm_EccSquared = .00669437999013;
        $phif = $this->FootpointLatitude($y);
        $ep2 = (pow($sm_a, 2.0) - pow($sm_b, 2.0)) / pow($sm_b, 2.0);
        $cf = cos($phif);
        $nuf2 = $ep2 * pow($cf, 2.0);
        $Nf = pow($sm_a, 2.0) / ($sm_b * sqrt(1 + $nuf2));
        $Nfpow = $Nf;
        $tf = tan($phif);
        $tf2 = $tf * $tf;
        $tf4 = $tf2 * $tf2;
        $x1frac = 1.0 / ($Nfpow * $cf);
        $Nfpow *= $Nf;
        $x2frac = $tf / (2.0 * $Nfpow);
        $Nfpow *= $Nf;
        $x3frac = 1.0 / (6.0 * $Nfpow * $cf);
        $Nfpow *= $Nf;
        $x4frac = $tf / (24.0 * $Nfpow);
        $Nfpow *= $Nf;
        $x5frac = 1.0 / (120.0 * $Nfpow * $cf);
        $Nfpow *= $Nf;
        $x6frac = $tf / (720.0 * $Nfpow);
        $Nfpow *= $Nf;
        $x7frac = 1.0 / (5040.0 * $Nfpow * $cf);
        $Nfpow *= $Nf;
        $x8frac = $tf / (40320.0 * $Nfpow);
        $x2poly = -1.0 - $nuf2;
        $x3poly = -1.0 - 2 * $tf2 - $nuf2;
        $x4poly = 5.0 + 3.0 * $tf2 + 6.0 * $nuf2 - 6.0 * $tf2 * $nuf2- 3.0 * ($nuf2 *$nuf2) - 9.0 * $tf2 * ($nuf2 * $nuf2);
        $x5poly = 5.0 + 28.0 * $tf2 + 24.0 * $tf4 + 6.0 * $nuf2 + 8.0 * $tf2 * $nuf2;
        $x6poly = -61.0 - 90.0 * $tf2 - 45.0 * $tf4 - 107.0 * $nuf2	+ 162.0 * $tf2 * $nuf2;
        $x7poly = -61.0 - 662.0 * $tf2 - 1320.0 * $tf4 - 720.0 * ($tf4 * $tf2);
        $x8poly = 1385.0 + 3633.0 * $tf2 + 4095.0 * $tf4 + 1575 * ($tf4 * $tf2);
        $philambda[0] = $phif + $x2frac * $x2poly * ($x * $x)
                + $x4frac * $x4poly * pow($x, 4.0)
                + $x6frac * $x6poly * pow($x, 6.0)
                + $x8frac * $x8poly * pow($x, 8.0);
            
        $philambda[1] = $lambda0 + $x1frac * $x
                + $x3frac * $x3poly * pow($x, 3.0)
                + $x5frac * $x5poly * pow($x, 5.0)
                + $x7frac * $x7poly * pow($x, 7.0);
            
        return $philambda;
    }

    public function FootpointLatitude($y)
    {
        $sm_b = 6356752.314;
        $sm_a = 6378137.0;
        $UTMScaleFactor = 0.9996;
        $sm_EccSquared = .00669437999013;
        $n = ($sm_a - $sm_b) / ($sm_a + $sm_b);
        $alpha_ = (($sm_a + $sm_b) / 2.0)* (1 + (pow($n, 2.0) / 4) + (pow($n, 4.0) / 64));
        $y_ = $y / $alpha_;
        $beta_ = (3.0 * $n / 2.0) + (-27.0 * pow($n, 3.0) / 32.0)+ (269.0 * pow($n, 5.0) / 512.0);
        $gamma_ = (21.0 * pow($n, 2.0) / 16.0)+ (-55.0 * pow($n, 4.0) / 32.0);
        $delta_ = (151.0 * pow($n, 3.0) / 96.0)+ (-417.0 * pow($n, 5.0) / 128.0);
        $epsilon_ = (1097.0 * pow($n, 4.0) / 512.0);
        $result = $y_ + ($beta_ * sin(2.0 * $y_))
                + ($gamma_ * sin(4.0 * $y_))
                + ($delta_ * sin(6.0 * $y_))
                + ($epsilon_ * sin(8.0 * $y_));
        return $result;
    }
    public function MapLatLonToXY($phi, $lambda, $lambda0)
    {
        $xy=array();
        $sm_b = 6356752.314;
        $sm_a = 6378137.0;
        $UTMScaleFactor = 0.9996;
        $sm_EccSquared = .00669437999013;
        $ep2 = (pow($sm_a, 2.0) - pow($sm_b, 2.0)) / pow($sm_b, 2.0);
        $nu2 = $ep2 * pow(cos($phi), 2.0);
        $N = pow($sm_a, 2.0) / ($sm_b * sqrt(1 + $nu2));
        $t = tan($phi);
        $t2 = $t * $t;
        $tmp = ($t2 * $t2 * $t2) - pow($t, 6.0);
        $l = $lambda - $lambda0;
        $l3coef = 1.0 - $t2 + $nu2;
        $l4coef = 5.0 - $t2 + 9 * $nu2 + 4.0 * ($nu2 * $nu2);
        $l5coef = 5.0 - 18.0 * $t2 + ($t2 * $t2) + 14.0 * $nu2- 58.0 * $t2 * $nu2;
        $l6coef = 61.0 - 58.0 * $t2 + ($t2 * $t2) + 270.0 * $nu2- 330.0 * $t2 * $nu2;
        $l7coef = 61.0 - 479.0 * $t2 + 179.0 * ($t2 * $t2) - ($t2 * $t2 * $t2);
        $l8coef = 1385.0 - 3111.0 * $t2 + 543.0 * ($t2 * $t2) - ($t2 * $t2 * $t2);
        $xy[0] = $N * cos($phi) * $l
                + ($N / 6.0 * pow(cos($phi), 3.0) * $l3coef * pow($l, 3.0))
                + ($N / 120.0 * pow(cos($phi), 5.0) * $l5coef * pow($l, 5.0))
                + ($N / 5040.0 * pow(cos($phi), 7.0) * $l7coef * pow($l, 7.0));
        $xy[1] = $this->ArcLengthOfMeridian($phi)
                + ($t / 2.0 * $N * pow(cos($phi), 2.0) * pow($l, 2.0))
                + ($t / 24.0 * $N * pow(cos($phi), 4.0) * $l4coef * pow($l, 4.0))
                + ($t / 720.0 * $N * pow(cos($phi), 6.0) * $l6coef * pow($l, 6.0))
                + ($t / 40320.0 * $N * pow(cos($phi), 8.0) * $l8coef * pow($l, 8.0));
        return $xy;
    }
    public function ArcLengthOfMeridian($phi)
    {
        $sm_b = 6356752.314;
        $sm_a = 6378137.0;
        $UTMScaleFactor = 0.9996;
        $sm_EccSquared = .00669437999013;
        $n = ($sm_a - $sm_b) / ($sm_a + $sm_b);
        $alpha = (($sm_a + $sm_b) / 2.0)
            * (1.0 + (pow($n, 2.0) / 4.0) + (pow($n, 4.0) / 64.0));
        $beta = (-3.0 * $n / 2.0) + (9.0 * pow($n, 3.0) / 16.0)
               + (-3.0 * pow($n, 5.0) / 32.0);
        $gamma = (15.0 * pow($n, 2.0) / 16.0)
                + (-15.0 * pow($n, 4.0) / 32.0);
        $delta = (-35.0 * pow($n, 3.0) / 48.0)
                + (105.0 * pow($n, 5.0) / 256.0);
        $epsilon = (315.0 * pow($n, 4.0) / 512.0);
        $result = $alpha* ($phi + ($beta * sin(2.0 * $phi))
                + ($gamma * sin(4.0 * $phi))
                + ($delta * sin(6.0 * $phi))
            + ($epsilon * sin(8.0 * $phi)));
        return $result;
    }
}
