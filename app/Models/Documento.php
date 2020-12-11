<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use HasFactory, SoftDeletes;

    //DEFINIMOS EL NOMBRE DE LA TABLA
    protected $table = 'documentos';
    //DEFINIMOS LA CLAVE PRIMARIA DE LA TABLA, AUTOMATICAMENTE SE LE ASIGNA AUTO INCREMENT
    protected $primaryKey = 'id';

    //CAMPOS QUE NO QUEREMOS QUE SE DEVUELVAN EN LAS CONSULTAS
    protected $hidden = ['updated_at','deleted_at'];

    //ATRIBUTOS DE LA TABLE
    protected $fillable = [
        'docu_codigo',
        'docu_nombre',
        'docu_estado',
        'docu_descripcion',
        'usuario_id',
        'tipo_documento_id',
        'departamento_id',
        'created_at',
        'docu_fecha_egreso'
    ];

        //RELACIÓN DIRECTA HACIA USUARIO
       public function usuario()
        {
            return $this->belongsTo('App\Models\Usuario');
        }
        //RELACIÓN INVERSA HACIA USUARIO
       public function tipo_documento()
        {
            return $this->belongsTo('App\Models\TipoDocumento');
        }
        //RELACIÓN DIRECTA HACIA DEPARTAMENTOS
       public function departamento()
        {
            return $this->belongsTo('App\Models\Departamento');
        }


}
