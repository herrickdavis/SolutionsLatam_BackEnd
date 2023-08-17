<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SetAreasController;
use App\Http\Controllers\Api\SetMatricesController;
use App\Http\Controllers\Api\SetLaboratoriosController;
use App\Http\Controllers\Api\SetTipoMuestrasController;
use App\Http\Controllers\Api\SetTipoDocumentosController;
use App\Http\Controllers\Api\SetUnidadesController;
use App\Http\Controllers\Api\SetMuestraController;
use App\Http\Controllers\Api\SetParametrosController;
use App\Http\Controllers\Api\SetMetodosController;
use App\Http\Controllers\Api\SetDataCampoController;
use App\Http\Controllers\Api\GetMuestrasController;
use App\Http\Controllers\Api\GetMuestraController;
use App\Http\Controllers\Api\GetLoginController;
use App\Http\Controllers\Api\GetDataCampoController;
use App\Http\Controllers\Api\GetDataCampoExcelController;
use App\Http\Controllers\Api\GetTipoMuestraController;
use App\Http\Controllers\Api\GetLimitesController;
use App\Http\Controllers\Api\GetEstacionesController;
use App\Http\Controllers\Api\GetParametrosController;
use App\Http\Controllers\Api\GetDataHistoricaController;
use App\Http\Controllers\Api\GetDataHistoricaExcelController;
use App\Http\Controllers\Api\GetReporteFueraLimitesController;
use App\Http\Controllers\Api\GetMatricesController;
use App\Http\Controllers\Api\GetParametrosReporteEstacionesController;
use App\Http\Controllers\Api\GetEstacionesReporteEstacionesController;
use App\Http\Controllers\Api\GetMapaEstacionesController;
use App\Http\Controllers\Api\GetReporteEstacionesController;
use App\Http\Controllers\Api\GetDocumentosMuestraController;
use App\Http\Controllers\Api\GetLogLoginController;
use App\Http\Controllers\Api\SetDocumentosMuestraController;
use App\Http\Controllers\Api\GetZipMuestraController;
use App\Http\Controllers\Api\GetDocumentoMuestraController;
use App\Http\Controllers\Api\GetEmpresasController;
use App\Http\Controllers\Api\GetInformesController;
use App\Http\Controllers\Api\GetDocumentoInformesController;
use App\Http\Controllers\Api\SetEmpresaHistoricoController;
use App\Http\Controllers\Api\GetCambiarPasswordController;
use App\Http\Controllers\Api\SetConsolidarEstacionesController;
use App\Http\Controllers\Api\GetReporteFueraLimiteExcelController;
use App\Http\Controllers\Api\SetConsolidarParametrosController;
use App\Http\Controllers\Api\SetCambiarEmpresaController;
use App\Http\Controllers\Api\SetColumnasUsuarioController;
use App\Http\Controllers\Api\SetUsuarioController;
use App\Http\Controllers\Api\GetColumnasController;
use App\Http\Controllers\Api\GetUsuariosController;
use App\Http\Controllers\Api\GetAutentificacionInformeController;
use App\Http\Controllers\Api\GetProyectosController;
use App\Http\Controllers\Api\SetEstadoMuestrasController;
use App\Http\Controllers\Api\GetLogsController;
use App\Http\Controllers\Api\GetLogAccionesController;
use App\Http\Controllers\Api\GetEmpresasPorUsuarioController;
use App\Http\Controllers\Api\GetAllEstacionesController;
use App\Http\Controllers\Api\SetAliasEstacionesController;
use App\Http\Controllers\Api\GetAllProyectosController;
use App\Http\Controllers\Api\SetAliasProyectosController;

use App\Http\Controllers\COC\GetDocumentoCOCController;
use App\Http\Controllers\COC\GetDataCOCController;
use App\Http\Controllers\COC\GetEmpresasCOCController;
use App\Http\Controllers\COC\SetDataCadenasController;
use App\Http\Controllers\COC\SetCadenaPlantillaController;
use App\Http\Controllers\COC\SetUpdatePlantillaController;
use App\Http\Controllers\COC\GetCadenaPlantillasController;
use App\Http\Controllers\COC\GetArchivoPlantillaController;
use App\Http\Controllers\COC\GetAllCadenasController;

// API CLIENTES
use App\Http\Controllers\ApiClientes\LoginController;
use App\Http\Controllers\ApiClientes\MuestrasController;

// Extras
use App\Http\Controllers\Extras\UpdateEmpresaMuestraController;
//EDD
use App\Http\Controllers\Api\SetEddController;
use App\Http\Controllers\Api\GetEddController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('SetAreas', SetAreasController::class);
Route::apiResource('SetMatrices', SetMatricesController::class);
Route::apiResource('SetLaboratorios', SetLaboratoriosController::class);
Route::apiResource('SetTipoMuestras', SetTipoMuestrasController::class);
Route::apiResource('SetTipoDocumentos', SetTipoDocumentosController::class);
Route::apiResource('SetUnidades', SetUnidadesController::class);
Route::apiResource('SetMuestra', SetMuestraController::class);
Route::apiResource('SetParametros', SetParametrosController::class);
Route::apiResource('SetMetodos', SetMetodosController::class);
Route::apiResource('SetDataCampo', SetDataCampoController::class);
Route::apiResource('SetDocumentosMuestra', SetDocumentosMuestraController::class);
Route::apiResource('SetRegistroUsuarios', SetRegistroUsuariosController::class);
Route::apiResource('GetLogin', GetLoginController::class);
Route::apiResource('GetEmpresas', GetEmpresasController::class);
Route::apiResource('SetEmpresaHistorico', SetEmpresaHistoricoController::class);
Route::apiResource('GetCambiarPassword', GetCambiarPasswordController::class);
Route::apiResource('SetConsolidarEstaciones', SetConsolidarEstacionesController::class);
Route::apiResource('SetConsolidarParametros', SetConsolidarParametrosController::class);
Route::apiResource('GetLogLogin', GetLogLoginController::class);
Route::apiResource('GetAutentificacionInforme', GetAutentificacionInformeController::class);
Route::apiResource('SetEstadoMuestras', SetEstadoMuestrasController::class);
Route::apiResource('GetLogs', GetLogsController::class);
Route::apiResource('GetLogAcciones', GetLogAccionesController::class);

//COC
Route::apiResource('GetClienteCOC', GetEmpresasCOCController::class);
Route::apiResource('SetDataCadenas', SetDataCadenasController::class);
Route::apiResource('SetCadenaPlantilla', SetCadenaPlantillaController::class);
Route::apiResource('SetUpdatePlantilla', SetUpdatePlantillaController::class);
Route::apiResource('GetCadenaPlantillas', GetCadenaPlantillasController::class);
Route::apiResource('GetArchivoPlantilla', GetArchivoPlantillaController::class);

//Extras
Route::apiResource('UpdateEmpresaMuestra', UpdateEmpresaMuestraController::class);


Route::middleware('auth:sanctum')->apiResource('GetMuestras', GetMuestrasController::class);
Route::middleware('auth:sanctum')->apiResource('GetMuestra', GetMuestraController::class);
Route::middleware('auth:sanctum')->apiResource('GetDataCampo', GetDataCampoController::class);
Route::middleware('auth:sanctum')->apiResource('GetDataCampoExcel', GetDataCampoExcelController::class);
Route::middleware('auth:sanctum')->apiResource('GetTipoMuestras', GetTipoMuestraController::class);
Route::middleware('auth:sanctum')->apiResource('GetLimites', GetLimitesController::class);
Route::middleware('auth:sanctum')->apiResource('GetEstaciones', GetEstacionesController::class);
Route::middleware('auth:sanctum')->apiResource('GetParametros', GetParametrosController::class);
Route::middleware('auth:sanctum')->apiResource('GetDataHistorica', GetDataHistoricaController::class);
Route::middleware('auth:sanctum')->apiResource('GetDataHistoricaExcel', GetDataHistoricaExcelController::class);
Route::middleware('auth:sanctum')->apiResource('GetReporteFueraLimites', GetReporteFueraLimitesController::class);
Route::middleware('auth:sanctum')->apiResource('GetMatrices', GetMatricesController::class);
Route::middleware('auth:sanctum')->apiResource('GetParametrosReporteEstaciones', GetParametrosReporteEstacionesController::class);
Route::middleware('auth:sanctum')->apiResource('GetEstacionesReporteEstaciones', GetEstacionesReporteEstacionesController::class);
Route::middleware('auth:sanctum')->apiResource('GetMapaEstaciones', GetMapaEstacionesController::class);
Route::middleware('auth:sanctum')->apiResource('GetReporteEstaciones', GetReporteEstacionesController::class);
Route::middleware('auth:sanctum')->apiResource('GetDocumentosMuestra', GetDocumentosMuestraController::class);
Route::middleware('auth:sanctum')->apiResource('GetZipMuestra', GetZipMuestraController::class);
Route::middleware('auth:sanctum')->apiResource('GetDocumentoMuestra', GetDocumentoMuestraController::class);
Route::middleware('auth:sanctum')->apiResource('GetInformes', GetInformesController::class);
Route::middleware('auth:sanctum')->apiResource('GetDocumentoInformes', GetDocumentoInformesController::class);
Route::middleware('auth:sanctum')->apiResource('GetReporteFueraLimiteExcel', GetReporteFueraLimiteExcelController::class);
Route::middleware('auth:sanctum')->apiResource('SetCambiarEmpresa', SetCambiarEmpresaController::class);
Route::middleware('auth:sanctum')->apiResource('SetColumnasUsuario', SetColumnasUsuarioController::class);
Route::middleware('auth:sanctum')->apiResource('SetUsuario', SetUsuarioController::class);
Route::middleware('auth:sanctum')->apiResource('GetColumnas', GetColumnasController::class);
Route::middleware('auth:sanctum')->apiResource('GetUsuarios', GetUsuariosController::class);
Route::middleware('auth:sanctum')->apiResource('GetProyectos', GetProyectosController::class);

Route::apiResource('GetDocumentoCOC', GetDocumentoCOCController::class);
Route::apiResource('GetDataCOC', GetDataCOCController::class);
Route::apiResource('GetAllCadenas', GetAllCadenasController::class);

Route::middleware('auth:sanctum')->apiResource('GetEmpresasPorUsuario', GetEmpresasPorUsuarioController::class);
Route::middleware('auth:sanctum')->apiResource('GetAllEstaciones', GetAllEstacionesController::class);
Route::middleware('auth:sanctum')->apiResource('SetAliasEstaciones', SetAliasEstacionesController::class);
Route::middleware('auth:sanctum')->apiResource('GetAllProyectos', GetAllProyectosController::class);
Route::middleware('auth:sanctum')->apiResource('SetAliasProyectos', SetAliasProyectosController::class);

//EDD
Route::apiResource('SetEdd', SetEddController::class);
Route::apiResource('GetEdd', GetEddController::class);


//Route::middleware('auth:sanctum')->apiResource('GetCambiarPassword', GetCambiarPasswordController::class);


//######## API PARA CLIENTE
//Route::middleware('auth:sanctum')->apiResource('Muestras', MuestrasController::class);
Route::apiResource('Login', LoginController::class);
Route::middleware(['auth:sanctum','throttle:6,1'])->apiResource('Muestras', MuestrasController::class);
