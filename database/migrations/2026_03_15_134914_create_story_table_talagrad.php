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
        Schema::create('story_table_talagrad', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text("text");
            $table->string("image")->nullable();;
            $table->string("title")->nullable();;
            $table->unsignedInteger('position')->index()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_table_talagrad');
    }
};
