<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStreamerIdToIpaymuLogsTable extends Migration
{
    /**
     * Tambahkan kolom streamer_id ke tabel pwp_ipaymu.
     */
    public function up()
    {
        Schema::table('pwp_ipaymu', function (Blueprint $table) {
            $table->unsignedBigInteger('streamer_id')->nullable()->after('user_id');
        });
    }

    /**
     * Rollback: hapus kolom streamer_id dari tabel pwp_ipaymu.
     */
    public function down()
    {
        Schema::table('pwp_ipaymu', function (Blueprint $table) {
            $table->dropColumn('streamer_id');
        });
    }
}
