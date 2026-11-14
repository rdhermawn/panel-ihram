<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Player;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman Home dengan Top 10 Player Ranking.
     */
    public function index()
    {
        // Ambil ranking berdasarkan level
        $ranks = Player::subtype('level')
            ->paginate(10);

        return view('front.home', [
            'ranks' => $ranks,
        ]);
    }
}