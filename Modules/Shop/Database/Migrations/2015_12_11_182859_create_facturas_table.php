<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->string('estado');
            $table->decimal('importe');
            $table->decimal('importe_abonado');
            $table->decimal('importe_pendiente');
            $table->timestamp('fecha_vencimiento');
            $table->timestamp('fecha_pago');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('facturas');
    }

}
