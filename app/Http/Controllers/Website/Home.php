<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Player;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Home extends Controller
{
    public function index()
    {
        $bulan = now()->month;
        $tahun = now()->year;

        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $topSpenders = DB::table('pwp_ipaymu as p')
            ->join('users as u', 'p.user_id', '=', 'u.id')
            ->leftJoin('users as s', 'p.streamer_id', '=', 's.id')
            ->select(
                'u.truename as name',
                DB::raw('COALESCE(s.streamer_name, "-") as streamer_name'),
                DB::raw('SUM(p.amount) as total_amount'),
                DB::raw('SUM(p.money) as total_money')
            )
            ->where('p.status', 'berhasil')
            ->whereBetween('p.created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('u.id', 'u.truename', 's.streamer_name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        $totalMoneyAllSpenders = DB::table('pwp_ipaymu')
            ->where('status', 'berhasil')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('money');

        // TOP 10 TOTAL KILL
        $ranks = Player::subtype('pvp')
            ->limit(10)
            ->get();

        return view('website.index', [
            'news' => News::orderBy('id', 'desc')
                ->whereNotIn('category', ['download', 'guide'])
                ->limit(4)
                ->get(),

            'ranks' => $ranks,
            'spouseNames' => Player::whereIn('id', $ranks->pluck('spouse')->filter())
                ->pluck('name', 'id'),

            // TOP 10 SPENDER
            'topSpenders' => $topSpenders,

            // TOTAL SPENDER BULAN INI
            'totalMoneyAllSpenders' => $totalMoneyAllSpenders,

            'monthLabel' => $startOfMonth->translatedFormat('F Y'),
        ]);
    }
}