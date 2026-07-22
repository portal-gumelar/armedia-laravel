<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('radcheck', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 64)->default('')->index();
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default('==');
            $table->string('value', 253)->default('');
        });

        Schema::create('radreply', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 64)->default('')->index();
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default('=');
            $table->string('value', 253)->default('');
        });

        Schema::create('radusergroup', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 64)->default('')->index();
            $table->string('groupname', 64)->default('');
            $table->integer('priority')->default(1);
        });

        Schema::create('radgroupreply', function (Blueprint $table) {
            $table->increments('id');
            $table->string('groupname', 64)->default('')->index();
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default('=');
            $table->string('value', 253)->default('');
        });

        Schema::create('radacct', function (Blueprint $table) {
            $table->bigIncrements('radacctid');
            $table->string('acctsessionid', 64)->default('');
            $table->string('acctuniqueid', 32)->default('')->unique();
            $table->string('username', 64)->default('')->index();
            $table->string('realm', 64)->default('')->nullable();
            $table->string('nasipaddress', 15)->default('')->index();
            $table->string('nasportid', 32)->default('')->nullable();
            $table->string('nasporttype', 32)->default('')->nullable();
            $table->dateTime('acctstarttime')->nullable()->index();
            $table->dateTime('acctupdatetime')->nullable();
            $table->dateTime('acctstoptime')->nullable()->index();
            $table->integer('acctinterval')->nullable();
            $table->integer('acctsessiontime')->unsigned()->nullable()->index();
            $table->string('acctauthentic', 32)->default('')->nullable();
            $table->string('connectinfo_start', 128)->default('')->nullable();
            $table->string('connectinfo_stop', 128)->default('')->nullable();
            $table->bigInteger('acctinputoctets')->nullable();
            $table->bigInteger('acctoutputoctets')->nullable();
            $table->string('calledstationid', 50)->default('')->nullable();
            $table->string('callingstationid', 50)->default('')->nullable();
            $table->string('acctterminatecause', 32)->default('')->nullable();
            $table->string('servicetype', 32)->default('')->nullable();
            $table->string('framedprotocol', 32)->default('')->nullable();
            $table->string('framedipaddress', 15)->default('')->nullable()->index();
            $table->string('framedipv6address', 45)->default('')->nullable();
            $table->string('framedipv6prefix', 45)->default('')->nullable();
            $table->string('framedinterfaceid', 44)->default('')->nullable();
            $table->string('delegatedipv6prefix', 45)->default('')->nullable();
            $table->string('class', 64)->default('')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radacct');
        Schema::dropIfExists('radgroupreply');
        Schema::dropIfExists('radusergroup');
        Schema::dropIfExists('radreply');
        Schema::dropIfExists('radcheck');
    }
};
