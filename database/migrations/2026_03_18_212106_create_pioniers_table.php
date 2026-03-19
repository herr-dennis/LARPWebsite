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
        Schema::create('pioniers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('waffen')->default("Nicht bekannt");;
            $table->text('text')->nullable();
            $table->string('image')->default("Nicht bekannt");
            $table->string("rang")->default("Nicht bekannt");
            $table->string("dienstjahre")->default("Nicht bekannt");
            $table->string("geburtstag")->default("Nicht bekannt");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pioniers');
    }
};
