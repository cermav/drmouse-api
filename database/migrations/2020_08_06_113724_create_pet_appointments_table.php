<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePetAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owners_id');
            $table->integer('pet_id');
            $table->date('date');
            $table->string('description');
            $table
                ->timestamp('created_at')
                ->nullable(true)
                ->default(null);
            $table
                ->timestamp('updated_at')
                ->nullable(true)
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_appointments');
    }
}
