<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hostingmodule__productos', function(Blueprint $table)
        {
            $table->string('type');
            $table->tinyInteger('gid');
            $table->renameColumn('nombre', 'name');
            $table->text('description');
            $table->tinyInteger('hidden');
            $table->tinyInteger('showdomainoptions');
            $table->integer('welcomeemail');
            $table->tinyInteger('stockcontrol');
            $table->integer('qty');
            $table->tinyInteger('proratabilling');
            $table->integer('proratadate');
            $table->integer('proratachargenextmonth');
            $table->string('paytype');
            $table->tinyInteger('allowqty');
            $table->string('subdomain');
            $table->string('autosetup');
            $table->string('servertype');
            $table->integer('servergroup');
            $table->renameColumn('limitedisco', 'disklimit');
            $table->renameColumn('limitecuota', 'bwlimit');
            $table->renameColumn('cuentasftp', 'ftpaccounts');
            $table->renameColumn('cuentasemail', 'emailaccounts');
            $table->integer('mysqldatabases');
            $table->integer('subdomains');
            $table->integer('parkeddomains');
            $table->integer('addondomains');
            $table->string('dedicatedip');
            $table->tinyInteger('cgiaccess');
            $table->tinyInteger('shellaccess');
            $table->tinyInteger('frontpageextensions');
            $table->integer('mailinglists');
            $table->dropColumn('servidor');
            $table->integer('server_id')->unsigned();
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
            $table->dropColumn('type');
            $table->dropColumn('gid');
            $table->renameColumn('name', 'nombre');
            $table->dropColumn('description');
            $table->dropColumn('hidden');
            $table->dropColumn('showdomainoptions');
            $table->dropColumn('welcomeemail');
            $table->dropColumn('stockcontrol');
            $table->dropColumn('qty');
            $table->dropColumn('proratabilling');
            $table->dropColumn('proratadate');
            $table->dropColumn('proratachargenextmonth');
            $table->dropColumn('paytype');
            $table->dropColumn('allowqty');
            $table->dropColumn('subdomain');
            $table->dropColumn('autosetup');
            $table->dropColumn('servertype');
            $table->dropColumn('servergroup');
            $table->renameColumn('disklimit', 'limitedisco');
            $table->renameColumn('bwlimit', 'limitecuota');
            $table->renameColumn('ftpaccounts', 'cuentasftp');
            $table->renameColumn('emailaccounts', 'cuentasemail');
            $table->dropColumn('mysqldatabases');
            $table->dropColumn('subdomains');
            $table->dropColumn('parkeddomains');
            $table->dropColumn('addondomains');
            $table->dropColumn('dedicatedip');
            $table->dropColumn('cgiaccess');
            $table->dropColumn('shellaccess');
            $table->dropColumn('frontpageextensions');
            $table->dropColumn('mailinglists');
            $table->string('servidor');
            $table->dropColumn('server_id');
        });
    }

}
