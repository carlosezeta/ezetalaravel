<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToHostingmoduleProductos extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hostingmodule__productos', function(Blueprint $table)
        {
            $table->string('sku')->after('id');
            $table->decimal('price', 20, 2)->after('sku');
            $table->index('sku');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hostingmodule__productos', function(Blueprint $table)
        {
            $table->dropColumn('sku');
            $table->dropColumn('price');
        });
    }

}
