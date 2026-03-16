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
        Schema::table('events', function (Blueprint $table) {
            // Add event_type column (string, nullable) after description
            $table->string('event_type')->nullable()->after('description');

            // Add is_featured column (boolean) after is_virtual
            $table->boolean('is_featured')->default(false)->after('is_virtual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'is_featured']);
        });
    }
};