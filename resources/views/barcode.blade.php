<!DOCTYPE html>
<html>
<head>
  <title>Laravel 8 Barcode Generator</title>
</head>
<body>
<div class="container text-center">
  <div class="row">
    <div class="col-md-8 offset-md-2">
       <h1 class="mb-5">Laravel 8 Barcode Generator</h1>
 {{--       <div>{!! DNS1D::getBarcodeHTML('4445645656', 'C39') !!}</div></br>
       <div>{!! DNS1D::getBarcodeHTML('4445645656', 'POSTNET') !!}</div></br> --}}
       <div>{!! DNS1D::getBarcodeHTML('4445645656', 'PHARMA', 2,100) !!}</div></br>
       <img src="data:image/png;base64,{{DNS1D::getBarcodePNG('MEMO-1245', 'C128', 2, 100,array(0,0,0),true)}}" alt="barcode" /></br>
       <img src="data:image/png;base64,{{DNS1D::getBarcodePNG('MEMO-1245', 'C128A', 2, 100,array(0,0,0),true)}}" alt="barcode" /></br>
       <img src="data:image/png;base64,{{DNS1D::getBarcodePNG('MEMO-1245', 'C128B', 2, 100,array(0,0,0),true)}}" alt="barcode" /></br>

       {{-- <div>{!! DNS2D::getBarcodeHTML('4445645656', 'QRCODE') !!}</div></br> --}}
       {{-- <div>{!! DNS1D::getBarcodeHTML('124asdasd5', 'UPCE', 2,100) !!}</div></br>
       <div>{!! DNS1D::getBarcodeHTML('4445645656', 'C128') !!}</div></br>
       <div>{!! DNS1D::getBarcodeHTML('4445645656', 'C128A') !!}</div></br>
       <div>{!! DNS1D::getBarcodeHTML('4445645656', 'C128C') !!}</div></br> --}}
      {{--  <div>{!! DNS1D::getBarcodeHTML('MEMO12554', 'C128B') !!}</div></br> --}}
     
      
    </div>
  </div>
</div>
</body>
</html>