<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_audios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('original_name');
            $table->string('stored_name');
            $table->string('folder');
            $table->string('disk');

            $table->unsignedBigInteger('size');
            $table->string('mime_type');
            $table->string('extension', 10);
            $table->float('duration')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('post_id');
            $table->unique(['disk', 'folder', 'stored_name']);
        });
    }

    public function down(): void
    {
        Schema::table('post_audios', function (Blueprint $table) {
            $table->dropIndex(['post_id']);
            $table->dropUnique(['disk', 'folder', 'stored_name']);
        });

        Schema::dropIfExists('post_audios');
    }
};

