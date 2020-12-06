<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('usuario', 100);
            $table->string('password', 100);
            $table->integer('usuario_rut', 0);
            $table->string('usuario_dv', 2);          
            $table->string('usuario_nombre', 100);
            $table->string('usuario_ape_paterno', 100);
            $table->string('usuario_ape_materno', 100);
            $table->string('usuario_correo', 50);
            $table->string('usuario_tipo', 20);   
            $table->string('usuario_telefono', 15)->nullable();  
            $table->string('usuario_funcion', 200)->nullable();  
            $table->string('usuario_anexo', 15)->nullable();  
            $table->unsignedBigInteger('departamento_id')->nullable();       
            $table->foreign('departamento_id')->references('id')->on('departamentos')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
