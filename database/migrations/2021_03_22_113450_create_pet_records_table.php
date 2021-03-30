<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePetRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pet_id');
            $table->timestamps();
            $table->string('description')->nullable(true)->default(null);
            $table->string('notes')->nullable(true)->default(null);
            $table->integer('doctor_id')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_records');
    }
}
