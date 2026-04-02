<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('progul')->nullable();
            $table->string('grade')->index(); // 7A, 8B, 9C

            // unik per kelas
            $table->unique(['name', 'grade']);

            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
