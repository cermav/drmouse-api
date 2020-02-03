<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('doctors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
                    ->on('users')->onDelete('cascade');
            $table->integer('state_id')->unsigned();
            $table->foreign('state_id')->references('id')
                    ->on('states')->onDelete('cascade');
            $table->string('search_name')->collation('utf8_general_ci')->nullable(true);
            $table->text('description')->collation('utf8_general_ci')->nullable(false);
            $table->string('slug')->nullable(false)->unique();
            $table->boolean('speaks_english')->nullable(false)->default(0);
            $table->string('phone', 20)->collation('utf8_general_ci')->nullable(false);
            $table->string('second_phone', 20)->collation('utf8_general_ci')->nullable(true)->default(null);
            $table->string('website')->collation('utf8_general_ci')->nullable(true)->default(null);
            $table->string('street')->collation('utf8_general_ci')->nullable(true)->default(null);
            $table->string('city')->collation('utf8_general_ci')->nullable(true)->default(null);
            $table->string('country')->collation('utf8_general_ci')->nullable(true)->default(null);
            $table->integer('post_code')->nullable(true)->default(null);
            $table->double('latitude')->nullable(false);
            $table->double('longitude')->nullable(false);
            $table->integer('working_doctors_count')->nullable(true);
            $table->text('working_doctors_names')->collation('utf8_general_ci')->nullable(true);
            $table->integer('nurses_count')->nullable(true);
            $table->integer('other_workers_count')->nullable(true);
            $table->boolean('gdpr_agreed')->nullable(false)->default(0);
            $table->dateTime('gdpr_agreed_date')->nullable(true)->default(null);
            $table->integer('profile_completedness')->nullable(false)->default(0);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE doctors ADD FULLTEXT doctor_text(search_name, description, street, city, country, working_doctors_names)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('doctors');
    }

}
