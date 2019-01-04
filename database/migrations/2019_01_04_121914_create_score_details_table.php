<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('score_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('score_id')->unsigned();
            $table->foreign('score_id')->references('id')
                    ->on('scores')->onDelete('cascade');
            $table->integer('score_item_id')->unsigned();
            $table->foreign('score_item_id')->references('id')
                    ->on('score_items')->onDelete('cascade');
            $table->integer('points')->nulable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('score_details');
    }

}
