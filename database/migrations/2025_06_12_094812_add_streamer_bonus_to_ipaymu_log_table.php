<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStreamerBonusToIpaymuLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
		public function up()
		{
			Schema::table('pwp_ipaymu', function (Blueprint $table) {
				$table->decimal('streamer_bonus', 10, 0)->nullable()->after('streamer_id');
			});
		}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pwp_ipaymu', function (Blueprint $table) {
            //
        });
    }
}
