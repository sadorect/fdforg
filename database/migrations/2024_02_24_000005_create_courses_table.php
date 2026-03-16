<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('intro_video_url')->nullable();
            $table->string('difficulty_level')->default('beginner'); // beginner, intermediate, advanced
            $table->integer('duration_minutes')->default(0);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->foreignId('instructor_id')->constrained('users');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->integer('max_students')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->json('prerequisites')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->boolean('is_certificate_enabled')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('enrollment_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->timestamps();
            
            $table->index(['status', 'is_featured']);
            $table->index('difficulty_level');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};