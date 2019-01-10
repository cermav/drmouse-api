<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDoctorObjectToDoctorsLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doctors_log', function (Blueprint $table) {
            $table->text('doctor_object')->collation('utf8_general_ci')->nullable(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doctors_log', function (Blueprint $table) {
            $table->dropColumn('doctor_object');
        });
    }
}
