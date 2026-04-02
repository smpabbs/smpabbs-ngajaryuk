<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->tinyInteger('day');
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->char('value', 1);
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
