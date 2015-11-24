<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePymentmethodOnCuentas extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hostingmodule__cuentas', function(Blueprint $table)
        {
            $table->renameColumn('pymentmethod', 'paymentmethod');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hostingmodule__cuentas', function(Blueprint $table)
        {
            $table->renameColumn('paymentmethod', 'pymentmethod');
        });
    }

}
