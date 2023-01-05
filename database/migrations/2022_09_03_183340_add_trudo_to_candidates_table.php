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
        Schema::table('candidates', function (Blueprint $table) {
            $table->boolean('own_housing')->nullable()->default(0)->after('real_status_work_id');
            $table->string('pesel')->nullable()->after('client_position_id');
            $table->string('account_number')->nullable()->after('pesel');
            $table->string('mothers_name')->nullable()->after('account_number');
            $table->string('fathers_name')->nullable()->after('mothers_name');
            $table->text('address')->nullable()->after('fathers_name');
            $table->string('city')->nullable()->after('address');
            $table->string('zip')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('own_housing');
            $table->dropColumn('pesel');
            $table->dropColumn('account_number');
            $table->dropColumn('mothers_name');
            $table->dropColumn('fathers_name');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('zip');
        });
    }
};
