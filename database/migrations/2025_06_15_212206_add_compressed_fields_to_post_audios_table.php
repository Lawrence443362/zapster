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
        Schema::table("post_audios", function (Blueprint $table) {
            $table->string('compressed_disk')->nullable();
            $table->string('compressed_folder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("post_audios", function (Blueprint $table) {
            $table->dropColumn(['compressed_disk', 'compressed_folder']);
        });
    }
};
