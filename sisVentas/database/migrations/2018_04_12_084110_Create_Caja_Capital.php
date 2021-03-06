<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCajaCapital extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('caja_Capital', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contrato_codigo');
            $table->string('tipo_movimiento');
             $table->string('tienda');
            $table->float('monto');
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
     Schema::drop('caja_Capital');
    }
}
