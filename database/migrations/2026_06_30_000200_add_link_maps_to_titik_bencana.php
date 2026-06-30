<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('titik_bencana', function (Blueprint $table) {
            $table->string('link_maps')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('titik_bencana', function (Blueprint $table) {
            $table->dropColumn('link_maps');
        });
    }
};
