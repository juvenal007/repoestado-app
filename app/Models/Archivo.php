<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archivo extends Model
{
    use HasFactory, SoftDeletes;

    //DEFINIMOS EL NOMBRE DE LA TABLA
    protected $table = 'archivos';
    //DEFINIMOS LA CLAVE PRIMARIA DE LA TABLA, AUTOMATICAMENTE SE LE ASIGNA AUTO INCREMENT
    protected $primaryKey = 'id';

    //CAMPOS QUE NO QUEREMOS QUE SE DEVUELVAN EN LAS CONSULTAS
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    //ATRIBUTOS DE LA TABLE
    protected $fillable = [
        'archivo_nombre',
        'archivo_extension',
        'archivo_url',
        'archivo_descripcion',
        'documento_id',

    ];
    // RELACION DIRECTA HACIA DOCUMENTO
    public function documento()
    {
        return $this->belongsTo('App\Models\Documento');
    }
}
