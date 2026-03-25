<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('learner_type', 50)->nullable()->after('bio');
            $table->string('location')->nullable()->after('learner_type');
            $table->string('country', 120)->nullable()->after('location');
            $table->string('phone_number', 50)->nullable()->after('country');
            $table->string('organization_name')->nullable()->after('phone_number');
            $table->timestamp('learner_profile_completed_at')->nullable()->after('organization_name');
            $table->timestamp('learner_profile_deferred_at')->nullable()->after('learner_profile_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'learner_type',
                'location',
                'country',
                'phone_number',
                'organization_name',
                'learner_profile_completed_at',
                'learner_profile_deferred_at',
            ]);
        });
    }
};