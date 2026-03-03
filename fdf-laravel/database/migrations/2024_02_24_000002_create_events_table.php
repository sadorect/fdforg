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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->text('excerpt')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('time')->nullable();
            $table->string('location')->nullable();
            $table->string('venue')->nullable();
            $table->string('price')->nullable();
            $table->string('registration_url')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('upcoming');
            $table->boolean('is_virtual')->default(false);
            $table->string('meeting_link')->nullable();
            $table->integer('max_attendees')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'start_date']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};