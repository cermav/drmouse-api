<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePetVaccinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_vaccines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pet_id');
            $table->string('description');
            $table->date('apply_date');
            $table->integer('valid');
            $table->integer('color');
            $table->integer('vaccine_id')->nullable(true)->default(null);
            $table->string('vaccine_name')->nullable(true)->default(null);
            $table->integer('doctor_id')->nullable(true)->default(null);
            $table->integer('price')->nullable(true)->default(null);
            $table->string('notes')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_vaccines');
    }
}
