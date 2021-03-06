<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PDF;

class PdfController extends Controller
{
    public static function generarPdf($data)
    {
        $pdf = PDF::loadView('folio', $data);
        $pdf->setPaper('letter', 'portrait');
        $fecha = date("Y-m-d-H-i-s");
        $final_name = "FOLIO-{$fecha}.pdf";

        $path = Storage::disk('folio')->put($final_name, $pdf->output());
        $url = "folio/".$final_name;
        $extension = File::extension($final_name);

        $data = [
            'archivo_nombre' => $final_name,
            'archivo_extension' => $extension,
            'archivo_url' => $url,
            'archivo_descripcion' => 'FOLIO',
            'documento_id' => $data['documento']->id
        ];



        return $data;
    }
}
