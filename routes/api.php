<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EstadoController;
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
Route::post('register', [AuthController::class, 'register']);
Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.index');
Route::get('/folio', [BarcodeController::class, 'folio'])->name('folio.index');
Route::get('/generar-pdf', [PdfController::class, 'generarPdf']);



Route::group(['middleware' => ['jwt.verify']], function () {

    // UPLOAD FILES
    Route::post('/file-upload/upload', [FileUploadController::class, 'do_upload']);
    

    // TIPO DOCUMENTOS
    Route::get('/tipo-documento/list', [TipoDocumentoController::class, 'list']);
    Route::get('/tipo-documento/details/{id}', [TipoDocumentoController::class, 'details']);
    Route::post('/tipo-documento/store', [TipoDocumentoController::class, 'add']);
    Route::put('/tipo-documento/update/{id}', [TipoDocumentoController::class, 'edit']);
    Route::delete('/tipo-documento/delete/{id}', [TipoDocumentoController::class, 'delete']);
    Route::get('/tipo-documento/pluck', [TipoDocumentoController::class, 'pluck']);

    // TIPO DEPARTAMENTOS
    Route::get('/departamento/list', [DepartamentoController::class, 'list']);
    Route::get('/departamento/details/{id}', [DepartamentoController::class, 'details']);
    Route::post('/departamento/store', [DepartamentoController::class, 'add']);
    Route::put('/departamento/update/{id}', [DepartamentoController::class, 'edit']);
    Route::delete('/departamento/delete/{id}', [DepartamentoController::class, 'delete']);
    Route::get('/departamento/pluck', [DepartamentoController::class, 'pluck']);

    // TIPO USUARIOS
    Route::get('/usuario/list/{departamento_id}', [UsuarioController::class, 'list']);
    Route::get('/usuario/details/{id}', [UsuarioController::class, 'details']);
    Route::post('/usuario/store', [UsuarioController::class, 'add']);
    Route::put('/usuario/update/{id}', [UsuarioController::class, 'edit']);
    Route::delete('/usuario/delete/{id}', [UsuarioController::class, 'delete']);
    Route::get('/usuario/pluck', [UsuarioController::class, 'pluck']);

    // DOCUMENTOS
    Route::get('/documento/list/', [DocumentoController::class, 'list']);
    Route::get('/documento/list/mis-documentos', [DocumentoController::class, 'mis_documentos']);
    Route::get('/documento/details/{id}', [DocumentoController::class, 'details']);
    Route::post('/documento/store', [DocumentoController::class, 'add']);
    Route::put('/documento/update/{id}', [DocumentoController::class, 'edit']);
    Route::delete('/documento/delete/{id}', [DocumentoController::class, 'delete']);
    Route::get('/documento/pluck', [DocumentoController::class, 'pluck']);

    // ESTADOS
    Route::get('/estado/list', [EstadoController::class, 'list']);
    Route::get('/estado/details/{id}', [EstadoController::class, 'details']);
    Route::post('/estado/store', [EstadoController::class, 'add']);
    Route::post('/estado/store/recibido', [EstadoController::class, 'recibido']);
    Route::post('/estado/store/terminado', [EstadoController::class, 'terminado']);
    Route::put('/estado/update/{id}', [EstadoController::class, 'edit']);
    Route::delete('/estado/delete/{id}', [EstadoController::class, 'delete']);
    Route::get('/estado/pluck', [EstadoController::class, 'pluck']);
});