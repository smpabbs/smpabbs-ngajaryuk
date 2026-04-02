<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            $table->string('class', 10);      // contoh: "7A"
            $table->string('subject', 50);    // contoh: "IPA"
            
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('users')
                  ->onDelete('set null');

            $table->date('date')->nullable(); // optional (gunakan month & year juga bisa)
            $table->string('time', 5);        // "14:30"
            $table->text('note');             // isi keterangan
            $table->boolean('checked')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
