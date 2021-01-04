<?php

use App\Exports\DocumentsExport;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']);
Route::post('refresh', [AuthController::class, 'refresh']);

// GENERAR CODIGO BARRA
Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.index');

// GENERAR FOLIO PDF
Route::get('/folio', [BarcodeController::class, 'folio'])->name('folio.index');

// GENERAR ARCHIVO DOCUMENTO
Route::get('/generar-pdf', [PdfController::class, 'generarPdf']);

//EXPORTS EXCEL
Route::get('/documento/export', [ExportController::class, 'export']);
Route::get('/documento/generar-excel', [DocumentsExport::class, 'list_usuario']);


Route::group(['middleware' => ['jwt.verify']], function () {

    // UPLOAD FILES
    Route::post('/file-upload/upload', [FileUploadController::class, 'do_upload']);

    // TIPO DOCUMENTOS
    Route::get('/tipo-documento/list', [TipoDocumentoController::class, 'list']);
    Route::get('/tipo-documento/list_all', [TipoDocumentoController::class, 'list_all']);
    Route::get('/tipo-documento/details/{id}', [TipoDocumentoController::class, 'details']);
    Route::post('/tipo-documento/store', [TipoDocumentoController::class, 'add']);
    Route::put('/tipo-documento/update/{id}', [TipoDocumentoController::class, 'edit']);
    Route::delete('/tipo-documento/delete/{id}', [TipoDocumentoController::class, 'delete']);
    Route::get('/tipo-documento/pluck', [TipoDocumentoController::class, 'pluck']);

    // ARCHIVOS
    Route::get('/archivo/list', [ArchivoController::class, 'list']);
    Route::get('/archivo/list_all', [ArchivoController::class, 'list_all']);
    Route::get('/archivo/ver_archivo/{documento_id}', [ArchivoController::class, 'verArchivo']);
    Route::get('/archivo/ver_folio/{documento_id}', [ArchivoController::class, 'verFolio']);
    Route::get('/archivo/details/{id}', [ArchivoController::class, 'details']);
    Route::post('/archivo/store', [ArchivoController::class, 'add']);
    Route::put('/archivo/update/{id}', [ArchivoController::class, 'edit']);
    Route::delete('/archivo/delete/{id}', [ArchivoController::class, 'delete']);
    Route::get('/archivo/pluck', [ArchivoController::class, 'pluck']);

    // TIPO DEPARTAMENTOS
    Route::get('/departamento/list', [DepartamentoController::class, 'list']);
    Route::get('/departamento/list_all', [DepartamentoController::class, 'list_all']);
    Route::get('/departamento/details/{id}', [DepartamentoController::class, 'details']);
    Route::post('/departamento/store', [DepartamentoController::class, 'add']);
    Route::put('/departamento/update/{id}', [DepartamentoController::class, 'edit']);
    Route::delete('/departamento/delete/{id}', [DepartamentoController::class, 'delete']);
    Route::get('/departamento/pluck', [DepartamentoController::class, 'pluck']);

    // TIPO USUARIOS
    Route::get('/usuario/list/{departamento_id}', [UsuarioController::class, 'list']);
    Route::get('/usuario/list_all', [UsuarioController::class, 'list_all']);
    Route::get('/usuario/details/{id}', [UsuarioController::class, 'details']);
    Route::post('/usuario/store', [UsuarioController::class, 'add']);
    Route::put('/usuario/update/{id}', [UsuarioController::class, 'edit']);
    Route::delete('/usuario/delete/{id}', [UsuarioController::class, 'delete']);
    Route::get('/usuario/pluck', [UsuarioController::class, 'pluck']);

    // DOCUMENTOS
    Route::get('/documento/list/', [DocumentoController::class, 'list']);
    // DEVUELVE TODOS LOS DOCUMENTOS CON SOFTDELETES
    Route::get('/documento/list/usuario_unique/{id_usuario}', [DocumentoController::class, 'list_usuario_unique']);
    Route::get('/documento/list/mis-documentos/usuario/{id_usuario}', [DocumentoController::class, 'list_usuario']);
    Route::get('/documento/list/mis-documentos/departamento/{id_departamento}', [DocumentoController::class, 'list_departamento']);
    Route::get('/documento/details/{codigo}', [DocumentoController::class, 'details']);
    Route::post('/documento/store', [DocumentoController::class, 'add']);
    Route::put('/documento/update/{id}', [DocumentoController::class, 'edit']);
    Route::delete('/documento/delete/{id}', [DocumentoController::class, 'delete']);
    Route::get('/documento/pluck', [DocumentoController::class, 'pluck']);

    // ESTADOS
    Route::get('/estado/list/{documento_id}', [EstadoController::class, 'list_documento']);
    Route::get('/estado/details/{id}', [EstadoController::class, 'details']);
    Route::post('/estado/store', [EstadoController::class, 'add']);
    Route::post('/estado/store/recibido', [EstadoController::class, 'recibido']);
    Route::post('/estado/store/terminado', [EstadoController::class, 'terminado']);
    Route::put('/estado/update/{id}', [EstadoController::class, 'edit']);
    Route::delete('/estado/delete/{id}', [EstadoController::class, 'delete']);
    Route::get('/estado/pluck', [EstadoController::class, 'pluck']);

    //DASHBOARD
    Route::get('/dashboard/barra', [DashController::class, 'grafico_barra']);
    Route::get('/dashboard/circular', [DashController::class, 'grafico_circular']);
});
