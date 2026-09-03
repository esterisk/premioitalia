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
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedInteger('convention_id')->nullable()->after('id');
            $table->string('last_name')->after('first_name');
            $table->unsignedBigInteger('user_id')->nullable()->after('email');
            $table->boolean('geo_verified')->default(false)->after('location');
            $table->text('notes')->nullable()->after('status');

            $table->json('location')->nullable()->change();
            $table->string('snapshot')->nullable()->change();

            $table->index(['ip_address', 'user_agent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['ip_address', 'user_agent']);
            $table->dropColumn(['convention_id', 'last_name', 'user_id', 'geo_verified', 'notes']);
        });
    }
};
