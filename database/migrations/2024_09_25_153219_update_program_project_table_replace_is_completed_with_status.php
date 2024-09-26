<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('program_project', function (Blueprint $table) {
            // Remove the 'is_completed' boolean column
            $table->dropColumn('is_completed');
            
            // Add the 'status' enum field with options
            $table->enum('status', ['not ready', 'ready', 'archived'])->default('not ready');

            // Add timestamps for status changes
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            // Remove the 'completed_at' if it's no longer needed
            $table->dropColumn('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_project', function (Blueprint $table) {
            // Re-add the 'is_completed' boolean column
            $table->boolean('is_completed')->default(false);

            // Remove the 'status' enum column
            $table->dropColumn('status');

            // Remove the status timestamps
            $table->dropColumn('ready_at');
            $table->dropColumn('archived_at');

            //re-add the 'completed_at' column if dropped earlier
            $table->timestamp('completed_at')->nullable();
        });
    }
};
