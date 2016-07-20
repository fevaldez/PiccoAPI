<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_unidad_negocio');
            $table->string('unidad_negocio');
            $table->string('descripcion')->nullable();
            $table->tinyInteger('acepta_mov');
            $table->tinyInteger('persona_moral');
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
        Schema::drop('business_unit');
    }
}
