<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_translations', function(Blueprint $table) {
            $table->increments('id');
            // Your translatable fields

            $table->integer('item_id')->unsigned();
            $table->string('locale')->index();
            $table->unique(['item_id', 'locale']);
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('item_translations');
    }
}
