<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->nullableMorphs('profile');  // This adds 'profile_id' and 'profile_type' columns
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropMorphs('profile');  // This drops both columns
        });
    }
}
