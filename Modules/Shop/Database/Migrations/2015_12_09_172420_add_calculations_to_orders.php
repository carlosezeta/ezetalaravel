<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCalculationsToOrders extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function(Blueprint $table)
        {
            $table->float('totalPrice');
            $table->float('totalTax');
            $table->float('totalShipping');
            $table->float('total');
            $table->float('totalDisccount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function(Blueprint $table)
        {
            $table->dropColumn('totalPrice');
            $table->dropColumn('totalTax');
            $table->dropColumn('totalShipping');
            $table->dropColumn('total');
            $table->dropColumn('totalDisccount');
        });
    }
}
