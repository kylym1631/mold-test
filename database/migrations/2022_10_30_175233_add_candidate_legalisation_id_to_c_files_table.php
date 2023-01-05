<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('c_files', function (Blueprint $table) {
            $table->foreignId('candidate_legalisation_id')->nullable()->after('oswiadczenie_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('c_files', function (Blueprint $table) {
            $table->dropColumn('candidate_legalisation_id');
        });
    }
};
