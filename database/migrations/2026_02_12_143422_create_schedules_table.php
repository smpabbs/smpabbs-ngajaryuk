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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('class_name'); // Nama kelas (7A, 7B, 8A, dst)
            $table->string('day'); // Hari (Monday, Tuesday, dst)
            $table->integer('period'); // Jam ke- (1, 2, 3, dst)
            $table->string('subject'); // Mata pelajaran (normalized: ICT, SPORT, dst)
            $table->string('subject_display')->nullable(); // Nama asli dari Excel
            $table->string('teacher')->nullable(); // Nama guru
            $table->time('start_time')->nullable(); // Jam mulai
            $table->time('end_time')->nullable(); // Jam selesai
            $table->timestamps();
            
            // Index untuk query yang lebih cepat
            $table->index(['class_name', 'day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
