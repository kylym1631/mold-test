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
        Schema::table('client_positions', function (Blueprint $table) {
            $table->decimal('min_rate_netto', 10, 2)->nullable()->after('personal_rate');
            $table->decimal('min_rate_brutto', 10, 2)->nullable()->after('min_rate_netto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_positions', function (Blueprint $table) {
            $table->dropColumn('min_rate_netto');
            $table->dropColumn('min_rate_brutto');
        });
    }
};
