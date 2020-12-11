<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estado extends Model
{
    use HasFactory, SoftDeletes;

    //DEFINIMOS EL NOMBRE DE LA TABLA
    protected $table = 'estados';
    //DEFINIMOS LA CLAVE PRIMARIA DE LA TABLA, AUTOMATICAMENTE SE LE ASIGNA AUTO INCREMENT
    protected $primaryKey = 'id';

    //CAMPOS QUE NO QUEREMOS QUE SE DEVUELVAN EN LAS CONSULTAS
    protected $hidden = ['created_at','updated_at','deleted_at'];

    //ATRIBUTOS DE LA TABLE
    protected $fillable = [
        'estado_nombre',
        'estado_descripcion',
        'estado_fecha_ingreso',
        'estado_fecha_egreso',
        'usuario_id',
        'departamento_id',
        'documento_id',

    ];

        //RELACIÓN DIRECTA HACIA USUARIO
       public function usuario()
        {
            return $this->belongsTo('App\Models\Usuario');
        }

        //RELACIÓN DIRECTA HACIA DEPARTAMENTO
       public function departamento()
        {
            return $this->belongsTo('App\Models\Departamento');
        }
        //RELACIÓN DIRECTA HACIA DOCUMENTO
       public function documento()
        {
            return $this->belongsTo('App\Models\Documento');
        }

}
