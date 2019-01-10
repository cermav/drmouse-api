<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormSectionDescriptionToPropertyCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_categories', function (Blueprint $table) {
            $table->text('form_section_description')->collation('utf8_general_ci')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_categories', function (Blueprint $table) {
            $table->dropColumn('form_section_description');
        });
    }
}
