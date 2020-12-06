<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDocumento extends Model
{
    use HasFactory, SoftDeletes;

    //DEFINIMOS EL NOMBRE DE LA TABLA
    protected $table = 'tipo_documentos';
    //DEFINIMOS LA CLAVE PRIMARIA DE LA TABLA, AUTOMATICAMENTE SE LE ASIGNA AUTO INCREMENT
    protected $primaryKey = 'id';

    //CAMPOS QUE NO QUEREMOS QUE SE DEVUELVAN EN LAS CONSULTAS
    protected $hidden = ['created_at','updated_at','deleted_at']; 

    //ATRIBUTOS DE LA TABLE
    protected $fillable = [
        'tipo_documento_nombre',  
        'tipo_documento_descripcion',  
    ];

       //RELACIÓN INVERSA HACIA DOCUMENTO
        public function documentos()
        {
            return $this->hasMany('App\Models\Documento');
        } 


}