<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('constructions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_proyecto');
            $table->string('nombre');
            $table->string('nombre_completo');
            $table->integer('id_unidad_negocio');
            $table->integer('id_ciudad');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->tinyInteger('cerrado');
            $table->tinyInteger('finiquitado');
            $table->integer('id_tipo_proyecto')->nullable();
            $table->integer('proyecto_padre')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('constructions');
    }
}
