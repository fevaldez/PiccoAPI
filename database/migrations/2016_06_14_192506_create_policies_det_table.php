<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoliciesDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policies_det', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_poliza_det');
            $table->integer('id_poliza');
            $table->integer('id_cuenta');
            $table->integer('id_proyecto');
            $table->string('observaciones')->nullable();
            $table->decimal('cargo', 19,4)->nullable();
            $table->decimal('abono', 19,4)->nullable();
            $table->string('moneda')->nullable();
            $table->double('tipo_cambio')->nullable();
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
        Schema::drop('policies_det');
    }
}
