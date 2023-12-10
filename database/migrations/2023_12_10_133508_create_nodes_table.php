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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_token_id')->constrained();
            $table->integer('inode')->nullable();
            $table->string('name', 255);
            $table->enum('type', ['directory', 'file']);
            $table->foreignId('parent_id')->nullable()->constrained('nodes');
            $table->foreignId('hard_link_id')->nullable()->constrained('nodes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
