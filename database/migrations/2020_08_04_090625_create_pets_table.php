<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owners_id');
            $table->string('pet_name');
            $table->date('birth_date');
            $table->string('kind');
            $table->string('breed');
            $table->integer('gender_state_id')->default(1);
            $table->integer('chip_number')->default(null);
            $table->string('background');
            $table->string('avatar');
            $table->timestamp('created_at')->default(null);
            $table->timestamp('updated_at')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pets');
    }
}
