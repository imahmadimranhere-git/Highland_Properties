<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Extends Laravel's default users table instead of replacing it,
    // so the framework's own auth migrations stay untouched.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->constrained()->restrictOnDelete();
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('designation', 120)->nullable()->after('avatar');
            // Deactivated users keep their data but cannot log in.
            $table->boolean('is_active')->default(true)->after('designation');
            $table->softDeletes();

            $table->index(['role_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role_id', 'is_active']);
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['phone', 'avatar', 'designation', 'is_active', 'deleted_at']);
        });
    }
};
