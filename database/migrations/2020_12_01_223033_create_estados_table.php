<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('estado_nombre', 100);         
            $table->text('estado_descripcion', 10)->nullable();  
            $table->dateTime('estado_fecha_ingreso', 0)->nullable();  
            $table->datetime('estado_fecha_egreso', 0)->nullable();   
            $table->unsignedBigInteger('usuario_id')->nullable();    
            $table->unsignedBigInteger('departamento_id')->nullable();    
            $table->unsignedBigInteger('documento_id')->nullable();    
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('departamento_id')->references('id')->on('departamentos')->onDelete('set null');
            $table->foreign('documento_id')->references('id')->on('documentos')->onDelete('set null');
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
        Schema::dropIfExists('estados');
    }
}
