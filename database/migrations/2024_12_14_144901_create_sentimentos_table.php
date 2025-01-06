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
        Schema::create('sentimentos', function (Blueprint $table) {
            $table->id();
            $table->integer('age');
            $table->string('stream_date');
            $table->string('analised_message');
            $table->string('sentiment');
            $table->double('goal');
            $table->integer('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentimentos');
    }
};
