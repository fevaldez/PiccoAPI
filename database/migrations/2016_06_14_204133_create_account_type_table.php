<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_tipo_cuenta');
            $table->integer('tipo_cuenta');
            $table->integer('sub_tipo_cuenta');
            $table->tinyInteger('naturaleza');
            $table->string('descripcion');
            $table->string('descripcion_subtipo');
            $table->integer('cuenta_inicial')->nullable();
            $table->integer('cuenta_final')->nullable();
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
        Schema::drop('account_type');
    }
}
