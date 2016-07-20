<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cuenta_mayor')->nullable();
            $table->string('cuenta')->nullable();
            $table->double('cargo')->nullable();
            $table->double('abono')->nullable();
            $table->integer(' year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('name_group');
            $table->integer('group_group');
            $table->integer('year_group');
            $table->integer('month_group');
            $table->integer('grouping_row');
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
        Schema::drop('balance');
    }
}
