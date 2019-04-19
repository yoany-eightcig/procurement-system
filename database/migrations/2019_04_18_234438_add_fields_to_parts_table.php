<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            //
            $table->string('vendor')->nullable()->default("");
            $table->integer('case_lot')->nullable()->default(0);
            $table->integer('suggest_qty')->nullable()->default(0);
            $table->integer('suggest_updated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parts', function (Blueprint $table) {
            //
            $table->dropColumn(['votes', 'avatar', 'location']);
        });
    }
}
