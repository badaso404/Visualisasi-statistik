<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ngobrol_statistik', function (Blueprint $table) {
            $table->string('presentasi')->nullable()->after('gambar');
        });
    }

    public function down(): void
    {
        Schema::table('ngobrol_statistik', function (Blueprint $table) {
            $table->dropColumn('presentasi');
        });
    }
};
