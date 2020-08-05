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
        Schema::create('Pets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owners_id');
            $table->timestamps();
            $table->string('pet_name');
            $table->date('birth_date');
            $table->string('kind');
            $table->string('breed');   
            $table->string('gender'); 
            $table->integer('chip_number');
            $table->string('bg');
            $table->integer('profile_completedness')->default('1');    
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
