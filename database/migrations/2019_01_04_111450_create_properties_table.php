<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_category_id')->unsigned();
            $table->foreign('property_category_id')->references('id')
                    ->on('property_categories')->onDelete('cascade');
            $table->string('name')->collation('utf8_general_ci')->nullable(false);
            $table->boolean('is_approved')->nullable(false)->default(0);
            $table->boolean('show_on_registration')->nullable(false)->default(0);
            $table->boolean('show_in_search')->nullable(false)->default(0);
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
        Schema::dropIfExists('properties');
    }
}
