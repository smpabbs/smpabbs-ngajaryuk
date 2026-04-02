<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // user_id sudah ada, jangan ditambah lagi
            if (!Schema::hasColumn('absensis', 'unit')) {
                $table->string('unit')->default('SMP ABBS Surakarta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};
