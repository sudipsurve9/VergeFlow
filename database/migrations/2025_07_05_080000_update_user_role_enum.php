<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateUserRoleEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }
        
        // Check current enum values
        $columnInfo = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'");
        if (empty($columnInfo)) {
            return; // Column doesn't exist
        }
        
        $column = $columnInfo[0];
        $currentType = $column->Type ?? '';
        
        // Check if enum already includes 'super_admin'
        if (strpos($currentType, 'super_admin') !== false) {
            return; // Already updated
        }
        
        // First, update any invalid role values to 'user'
        // Get all unique role values
        $roles = DB::table('users')->distinct()->pluck('role')->toArray();
        $validRoles = ['user', 'admin', 'super_admin'];
        
        foreach ($roles as $role) {
            if (!in_array($role, $validRoles)) {
                // Update invalid roles to 'user'
                DB::table('users')
                    ->where('role', $role)
                    ->update(['role' => 'user']);
            }
        }
        
        // Now safely modify the enum
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'super_admin') DEFAULT 'user'");
        } catch (\Exception $e) {
            // If it still fails, try a different approach - change to VARCHAR first, then back to ENUM
            if (strpos($e->getMessage(), 'truncated') !== false || strpos($e->getMessage(), 'Data truncated') !== false) {
                // Change to VARCHAR temporarily
                DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) DEFAULT 'user'");
                // Update any remaining invalid values
                DB::table('users')
                    ->whereNotIn('role', $validRoles)
                    ->update(['role' => 'user']);
                // Change back to ENUM
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'super_admin') DEFAULT 'user'");
            } else {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin') DEFAULT 'user'");
    }
} 