<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');
            $table->integer('state_id')->unsigned();
            $table->foreign('state_id')->references('id')
                ->on('states')->onDelete('cascade');
            $table->text('description')->collation('utf8_general_ci')->nullable(false);
            $table->string('slug')->nullable(false)->unique();
            $table->boolean('gdpr_agreed')->nullable(false)->default(0);
            $table->dateTime('gdpr_agreed_date')->nullable(true)->default(null);
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
        Schema::dropIfExists('members');
    }
}
