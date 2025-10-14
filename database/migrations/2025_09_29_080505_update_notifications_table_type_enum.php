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
        // SQLite doesn't support MODIFY COLUMN, so we'll recreate the table
        Schema::dropIfExists('notifications_backup');
        
        // Create backup table
        Schema::create('notifications_backup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
        
        // Copy existing data to backup
        $notifications = DB::table('notifications')->get();
        foreach ($notifications as $notification) {
            DB::table('notifications_backup')->insert([
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
            ]);
        }
        
        // Drop and recreate notifications table
        Schema::dropIfExists('notifications');
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'error', 'success', 'incident'])->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('is_read');
        });
        
        // Restore data from backup
        $backupNotifications = DB::table('notifications_backup')->get();
        foreach ($backupNotifications as $notification) {
            DB::table('notifications')->insert([
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
            ]);
        }
        
        // Drop backup table
        Schema::dropIfExists('notifications_backup');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('info', 'warning', 'error', 'success') DEFAULT 'info'");
    }
};
