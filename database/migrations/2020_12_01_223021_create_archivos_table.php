<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->string('archivo_nombre', 100);         
            $table->string('archivo_extension', 10);
            $table->string('archivo_url', 200);
            $table->text('archivo_descripcion')->nullable();   
            $table->unsignedBigInteger('documento_id')->nullable();    
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
        Schema::dropIfExists('archivos');
    }
}
