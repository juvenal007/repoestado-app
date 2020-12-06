<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index()
    {
      return view('barcode');
    }
    public function folio()
    {
      return view('folio');
    }
}
