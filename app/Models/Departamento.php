<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use HasFactory, SoftDeletes;

    //DEFINIMOS EL NOMBRE DE LA TABLA
    protected $table = 'departamentos';
    //DEFINIMOS LA CLAVE PRIMARIA DE LA TABLA, AUTOMATICAMENTE SE LE ASIGNA AUTO INCREMENT
    protected $primaryKey = 'id';

    //CAMPOS QUE NO QUEREMOS QUE SE DEVUELVAN EN LAS CONSULTAS
    protected $hidden = ['created_at','updated_at','deleted_at'];

    //ATRIBUTOS DE LA TABLE
    protected $fillable = [
        'depto_nombre',
        'depto_descripcion',
        'depto_telefono',
        'depto_anexo',
    ];

        //RELACIÓN INVERSA HACIA USUARIO
       public function usuarios()
        {
            return $this->hasMany('App\Models\Usuario');
        }
        //RELACIÓN INVERSA HACIA ESTADO
       public function estados()
        {
            return $this->hasMany('App\Models\Estado');
        }
        //RELACIÓN INVERSA HACIA ESTADO
       public function documentos()
        {
            return $this->hasMany('App\Models\Documentos');
        }


}
