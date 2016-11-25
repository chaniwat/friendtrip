<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditUsersEventsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sex');
            $table->enum('gender', ['MALE', 'FEMALE']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('sex_allow');
            $table->dropColumn('religion_allow');

            $table->enum('state', ['ON', 'CANCEL'])->default('ON');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
