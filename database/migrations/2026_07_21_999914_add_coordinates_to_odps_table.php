<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('odps', function (Blueprint $table) {
            if (!Schema::hasColumn('odps', 'latitude'))
                $table->decimal('latitude', 10, 7)->nullable()->after('code');
            if (!Schema::hasColumn('odps', 'longitude'))
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            if (!Schema::hasColumn('odps', 'alamat'))
                $table->text('alamat')->nullable()->after('longitude');
            if (!Schema::hasColumn('odps', 'port_terpakai'))
                $table->integer('port_terpakai')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('odps', function (Blueprint $table) {
            $table->dropColumnIfExists(['latitude', 'longitude', 'alamat', 'port_terpakai']);
        });
    }
};
