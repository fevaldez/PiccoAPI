<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_poliza');
            $table->integer('mes_poliza');
            $table->integer('id_tipo_poliza');
            $table->integer('num_poliza');
            $table->integer('id_proyecto');
            $table->date('fecha');
            $table->string('descripcion')->nullable();
            $table->tinyInteger('cancelada');
            $table->integer('id_poliza_origen')->nullable();
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
        Schema::drop('policies');
    }
}
