<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventSettingValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_setting_value', function (Blueprint $table) {
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('event_setting_id');
            $table->text('value');

            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('event_setting_id')->references('id')->on('event_settings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_setting_value');
    }
}
