<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('author_id')->unsigned()->nullable(true);
            $table->integer('status_id')->unsigned()->nullable(true);
            $table->foreign('user_id')->references('id')
                    ->on('users')->onDelete('cascade');
            $table->text('comment')->collation('utf8_general_ci')->nullable(true);
            $table->string('ip_address', 39)->nullable(true);
            $table->integer('verified_by')->unsigned()->nullable(true);
            $table->dateTime('verify_date')->nullable(true);
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
        Schema::dropIfExists('scores');
    }
}
