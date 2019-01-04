<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpeningHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('weekday_id')->unsigned();
            $table->foreign('weekday_id')->references('id')
                    ->on('weekdays')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
                    ->on('users')->onDelete('cascade');
            $table->time('open_at')->nullable(true);
            $table->time('close_at')->nullable(true);
            $table->boolean('is_nonstop')->nullable(false)->default(0);
            $table->boolean('is_closed')->nullable(false)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opening_hours');
    }
}
