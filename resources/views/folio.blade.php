<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Comprobante Documento</title>
</head>
<style>
        body {
      font-family: Arial, Helvetica, sans-serif;
    }
    table {
      border-collapse: collapse;
      margin: auto;
      width: 100% !important;
    }
    table,
    th,
    td {
      font-size: 0.7rem;
      border: 1px solid black;
      padding: 5px;
    }
    .noTable,
    .Cth,
    .Ctd{
        font-size: 0.7rem;
        padding: 5px;
        margin: none;
        border: none;
    }
    .logoLeft {
      text-align: left;   
      float: left;
    }
    .logoRight {
      text-align: right;     
      float: right;
    }
    .textCenter {
      text-align: center;
    }
    .bold {
      font-weight: bold;
    }
    .textRight {
      text-align: right;
    }
    .imgCenter {
      text-align: center;
      display: block;
    }
    .paddingLeftRight {
      padding-left: 30px;
      padding-right: 30px;
    }
    .paddingRight {
      margin-right: 0px;
    }
    .textJustify {
      text-align: justify;
      text-justify: inter-word;
    }
    .mt-1 {
        margin-top: 1.5rem;
    }
    .mb-1 {
        margin-bottom: 0.5rem;
    }
    .mt{
        margin-top: 0;
    }
</style>
<body>
<div class="container text-center">
  <div class="row">        
        <table class="noTable">
            <tbody>
                <tr>
                    <td class="Ctd">
                    <img class='logoLeft' src='{{public_path()."/EscudoPencahue150.png"}}' height="75px" />  
                    </td>
                    <td class="Ctd">
                        <span><h1 class="textCenter">Comprobante Documento {!! $documento['tipo_documento']->tipo_documento_nombre !!}</h1></span>
                    </td>
                     <td class="Ctd">
                        <img class='logoRight' src='{{public_path()."/LogoPencahue150.png"}}' height="75px" />
                    </td>
                </tr>
            </tbody>
        </table>  
        <span class=""><h2>Información de origen.</h2></span>
        <table width="50%" class="">
            <tbody width="50%">
                <tr>
                    <td width="50%">
                       <span>Rut:</span>
                    </td>
                    <td width="50%">
                    <span class="bold">{!! $user->usuario_rut."-".$user->usuario_dv !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span>Nombre:</span>
                    </td>
                    <td width="50%">
                        <span class="bold">{!! $user->usuario_nombre." ".$user->usuario_ape_paterno." ".$user->usuario_ape_materno !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span>Anexo:</span>
                    </td>
                    <td width="50%">
                        <span class="bold">{!! $user->usuario_anexo !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span>Departamento:</span>
                    </td>
                    <td width="50%">
                        <span class="bold">{!! $user['departamento']->depto_nombre !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span >Departamento Anexo:</span>
                    </td>
                    <td width="50%">
                        <span class="bold">{!! $user['departamento']->depto_anexo !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>     
        <span><h2  class="mt">Datos del Documento</h2></span>
        <table width="50%" class="">
            <tbody width="50%">
                <tr>
                    <td width="50%">
                       <span>Código:</span>
                    </td>
                    <td width="50%">
                        <span class="bold">{!! $documento->docu_codigo  !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span>Tipo:</span>
                    </td>
                    <td width="50%">
                        <span class="bold">{!! $documento['tipo_documento']->tipo_documento_nombre !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span>Fecha Ingreso Origen:</span>
                    </td>
                    <td width="50%">
                        <span class="bold">{!! $documento->created_at !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span>Nombre Documento:</span>
                    </td>
                    <td width="50%">
                        <span class="bold textJustify">{!! $documento->docu_nombre !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span >Nombre Adjunto:</span>
                    </td>
                    <td width="50%">
                        <span class="bold textJustify">{!! $archivo->archivo_nombre !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span >Descripción:</span>
                    </td>
                    <td width="50%">
                        <span class="bold textJustify">{!! $documento->docu_descripcion !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                       <span >Estado:</span>
                    </td>
                    <td width="50%">
                        <span class="bold">{!! $documento->docu_estado !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </br>
     

       <center><img class='mt-1 textCenter' src="data:image/png;base64,{{DNS1D::getBarcodePNG($documento->docu_codigo, 'C128', 2, 100,array(0,0,0),true)}}" alt="barcode" /></center>
      

      
     
      
    
  </div>
</div>
</body>
</html>