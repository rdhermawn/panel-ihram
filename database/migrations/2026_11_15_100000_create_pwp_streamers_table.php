<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePwpStreamersTable extends Migration
{
    public function up()
    {
        Schema::create('pwp_streamers', function (Blueprint $table) {
            $table->id();
            // users.ID of the account that receives the streamer bonus coins
            $table->unsignedBigInteger('user_id');
            $table->string('name', 50);
            $table->string('code', 30)->unique();
            $table->decimal('bonus_percent', 5, 2);
            $table->timestamps();
        });

        // The streamers that used to live in config/ipaymu.php
        $now = now();
        $streamers = [
            ['MELEDAK', 3056, 'MELEDAK'],
            ['GANGSTA', 3856, 'GANGSTA'],
            ['MASTAH', 3376, 'MASTAH'],
            ['MAMINA', 4224, 'MAMINA'],
            ['Joseph', 4512, 'JOY'],
            ['Levy', 9968, 'LEVAY'],
            ['Apip', 11072, 'PTK'],
            ['Riduan Iman', 4496, 'CRACKZ'],
            ['Wancoy', 22896, 'WANCOY'],
            ['Firjaben', 1792, 'Firjaben'],
            ['Deepsy', 15552, 'DEEPSY'],
        ];

        DB::table('pwp_streamers')->insert(array_map(static fn ($s) => [
            'name' => $s[0],
            'user_id' => $s[1],
            'code' => $s[2],
            'bonus_percent' => 30,
            'created_at' => $now,
            'updated_at' => $now,
        ], $streamers));
    }

    public function down()
    {
        Schema::dropIfExists('pwp_streamers');
    }
}
