<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('docu_codigo', 100);
            $table->string('docu_nombre', 100);
            $table->string('docu_estado', 100);
            $table->text('docu_descripcion')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('tipo_documento_id')->nullable();
            $table->unsignedBigInteger('departamento_id')->nullable();
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('tipo_documento_id')->references('id')->on('tipo_documentos')->onDelete('set null');
            $table->foreign('departamento_id')->references('id')->on('departamentos')->onDelete('set null');
            $table->timestamps();
            $table->dateTime('docu_fecha_egreso', 0)->nullable();
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
        Schema::dropIfExists('documentos');
    }
}
