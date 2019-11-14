<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScoreVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_votes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('score_id')->unsigned();
            $table->foreign('score_id')->references('id')
                ->on('scores')->onDelete('cascade');
            $table->integer('author_id')->unsigned()->nullable(true);
            $table->string('ip_address', 39)->nullable(true);
            $table->integer('value');
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
        Schema::dropIfExists('score_votes');
    }
}
