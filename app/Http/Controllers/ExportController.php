<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\DocumentsExport;
use Maatwebsite\Excel\Facades\Excel;


class ExportController extends Controller
{
    public function export()
    {
        $fecha = date("d-m-Y-H-i-s");
        $nombreDocumento = 'DocumentosPendientes'."-".$fecha.".xlsx";
        return Excel::download(new DocumentsExport, $nombreDocumento);
    }
}
