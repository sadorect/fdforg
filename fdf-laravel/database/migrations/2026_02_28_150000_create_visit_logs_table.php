<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('visit_type', 20)->index(); // site, page
            $table->string('path', 255)->nullable()->index();
            $table->string('route_name', 191)->nullable()->index();
            $table->text('full_url')->nullable();
            $table->string('session_id', 191)->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->string('device_type', 30)->nullable()->index();
            $table->string('browser', 50)->nullable()->index();
            $table->boolean('is_authenticated')->default(false)->index();
            $table->timestamp('visited_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
