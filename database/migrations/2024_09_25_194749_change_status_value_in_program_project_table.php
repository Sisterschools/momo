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
            // Remove the 'status' enum column if it exists
            if (Schema::hasColumn('program_project', 'status')) {
                $table->dropColumn('status');
            }

            // Add the 'status' enum field with updated options
            $table->enum('status', ['not_ready', 'ready', 'archived'])->default('not_ready');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_project', function (Blueprint $table) {
            // Remove the 'status' enum column if it exists
            if (Schema::hasColumn('program_project', 'status')) {
                $table->dropColumn('status');
            }

            // Re-add the 'status' enum field with original options
            $table->enum('status', ['not ready', 'ready', 'archived'])->default('not ready');
        });
    }
};
