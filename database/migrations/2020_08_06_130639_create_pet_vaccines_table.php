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
            $table->string('vaccine_name')->default(null);
            $table->integer('doctor_id')->default(null);
            $table->integer('price')->default(null);
            $table->string('notes')->default(null);
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
