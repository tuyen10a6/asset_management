<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table
        Schema::dropIfExists('users_backup');

        // Create backup table
        Schema::create('users_backup', function (Blueprint $table) {
            $table->id();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Copy existing data to backup
        DB::table('users_backup')->insert(
            DB::table('users')->select(['id', 'password', 'remember_token', 'created_at', 'updated_at'])->get()->toArray()
        );

        // Drop and recreate users table
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->foreignId('employee_id')->unique()->constrained('employees');
            $table->enum('role', ['admin', 'hr', 'user'])->default('user');
            $table->timestamp('last_login')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index('username');
            $table->index('role');
            $table->index('is_active');
        });

        // Restore data from backup
        DB::table('users')->insert(
            DB::table('users_backup')->select(['id', 'password', 'remember_token', 'created_at', 'updated_at'])->get()->toArray()
        );

        // Drop backup table
        Schema::dropIfExists('users_backup');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'employee_id', 'role', 'last_login', 'is_active']);
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }
};
