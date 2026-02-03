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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('poll_option_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('voted_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint: one vote per IP per poll
            $table->unique(['poll_id', 'ip_address', 'is_active'], 'unique_poll_ip_active');
            $table->index('poll_option_id');
            $table->index('ip_address');
            $table->index('voted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
