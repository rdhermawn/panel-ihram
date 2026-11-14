<?php

/*
 * @author Harris Marfel <hrace009@gmail.com>
 * @link https://youtube.com/c/hrace009
 * @copyright Copyright (c) 2022.
 */

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Helper\TimeMaker;
use App\Models\Faction;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// Export Excel
use App\Exports\TopSpenderExport;
use Maatwebsite\Excel\Facades\Excel;

class RankingController extends Controller
{
    /**
     * Redirect ke default ranking player berdasarkan level.
     */
    public function getIndex()
    {
        return redirect()->route('app.ranking.player', 'level');
    }

    /**
     * Ranking pemain berdasarkan tipe:
     * level, rep, time, dll.
     */
    public function getPlayers(Request $request)
    {
        $type = $request->segment(4);

        $query = Player::subtype($type);

        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%' . $request->input('search') . '%'
            );
        }

        if ($request->filled('class')) {
            $query->where(
                'cls',
                $request->input('class')
            );
        }

        $ranks = $query->paginate();

        $players = new Player();

        $timeMaker = new TimeMaker();

        return view('front.ranking.players', [
            'ranks' => $ranks,
            'players' => $players,
            'timeMaker' => $timeMaker,
        ]);
    }

    /**
     * Ranking faction.
     */
    public function getFactions(Request $request)
    {
        $ranks = Faction::subtype(
            $request->segment(4)
        )->paginate();

        return view('front.ranking.factions', [
            'ranks' => $ranks,
        ]);
    }

    /**
     * Top Spenders semua waktu.
     */
    public function getTopSpenders()
    {
        $topSpenders = DB::table('ipaymu')
            ->select(
                'user_id',
                DB::raw('SUM(money) as total_spent')
            )
            ->where('status', 'berhasil')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->paginate(10);

        $topSpenders->getCollection()->transform(function ($item) {

            $player = Player::find($item->user_id);

            $item->name =
                $player->streamer_name
                ?? $player->name
                ?? 'Unknown';

            return $item;
        });

        return view('front.ranking.spender', [
            'topSpenders' => $topSpenders,
        ]);
    }

    /**
     * ================================================================
     * TOP SPENDER COIN PER BULAN
     * ================================================================
     *
     * Contoh URL:
     *
     * ?month=4&year=2026
     *
     * Yang akan berubah mengikuti bulan:
     *
     * - Ranking
     * - Total Coin setiap user
     * - Total Rupiah setiap user
     * - Total Spender keseluruhan
     * - Jumlah spender
     * - Pagination
     *
     */
    public function getTopSpenderCoin(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | BULAN
        |--------------------------------------------------------------------------
        |
        | Mendukung:
        |
        | ?month=4
        |
        | dan format lama:
        |
        | ?bulan=4
        |
        */

        $selectedMonth = (int) $request->query(
            'month',
            $request->query(
                'bulan',
                now()->month
            )
        );

        /*
        |--------------------------------------------------------------------------
        | TAHUN
        |--------------------------------------------------------------------------
        |
        | Mendukung:
        |
        | ?year=2026
        |
        | dan format lama:
        |
        | ?tahun=2026
        |
        */

        $selectedYear = (int) $request->query(
            'year',
            $request->query(
                'tahun',
                now()->year
            )
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDASI BULAN
        |--------------------------------------------------------------------------
        */

        if (
            $selectedMonth < 1 ||
            $selectedMonth > 12
        ) {
            $selectedMonth = now()->month;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI TAHUN
        |--------------------------------------------------------------------------
        */

        if (
            $selectedYear < 2020 ||
            $selectedYear > now()->year
        ) {
            $selectedYear = now()->year;
        }

        /*
        |--------------------------------------------------------------------------
        | RANGE TANGGAL BULAN
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | April 2026
        |
        | >= 2026-04-01 00:00:00
        | <  2026-05-01 00:00:00
        |
        */

        $startOfMonth = Carbon::create(
            $selectedYear,
            $selectedMonth,
            1,
            0,
            0,
            0
        )->startOfMonth();

        $nextMonth = $startOfMonth
            ->copy()
            ->addMonth();

        /*
        |--------------------------------------------------------------------------
        | QUERY RANKING SPENDER
        |--------------------------------------------------------------------------
        */

        $topSpenders = DB::table('pwp_ipaymu as p')

            /*
             * User yang melakukan donasi
             */
            ->join(
                'users as u',
                'p.user_id',
                '=',
                'u.id'
            )

            /*
             * Streamer referral
             */
            ->leftJoin(
                'users as s',
                'p.streamer_id',
                '=',
                's.id'
            )

            /*
             * Transaksi berhasil saja
             */
            ->where(
                'p.status',
                'berhasil'
            )

            /*
             * Mulai bulan
             */
            ->where(
                'p.created_at',
                '>=',
                $startOfMonth
            )

            /*
             * Sampai sebelum bulan berikutnya
             */
            ->where(
                'p.created_at',
                '<',
                $nextMonth
            )

            /*
             * Kolom yang akan ditampilkan
             */
            ->select(
                'u.id as user_id',

                'u.truename as name',

                DB::raw(
                    'COALESCE(MAX(s.streamer_name), "-") as streamer_name'
                ),

                DB::raw(
                    'COALESCE(SUM(p.amount), 0) as total_amount'
                ),

                DB::raw(
                    'COALESCE(SUM(p.money), 0) as total_money'
                )
            )

            /*
             * Gabungkan transaksi per user.
             */
            ->groupBy(
                'u.id',
                'u.truename'
            )

            /*
             * Rank pertama berdasarkan coin terbesar.
             */
            ->orderByDesc(
                'total_amount'
            )

            /*
             * Jika coin sama,
             * urutkan berdasarkan rupiah.
             */
            ->orderByDesc(
                'total_money'
            )

            /*
             * 10 spender per halaman.
             */
            ->paginate(10)

            /*
             * Pertahankan month/year
             * ketika pindah pagination.
             */
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | TOTAL RUPIAH BULAN TERPILIH
        |--------------------------------------------------------------------------
        |
        | INI YANG DIPAKAI:
        |
        | Rp. 8.250.000
        |
        | pada kotak Total Spender.
        |
        */

        $totalMoneyAllSpenders = DB::table(
            'pwp_ipaymu'
        )
            ->where(
                'status',
                'berhasil'
            )

            ->where(
                'created_at',
                '>=',
                $startOfMonth
            )

            ->where(
                'created_at',
                '<',
                $nextMonth
            )

            ->sum('money');

        /*
        |--------------------------------------------------------------------------
        | TOTAL COIN BULAN TERPILIH
        |--------------------------------------------------------------------------
        */

        $totalCoinAllSpenders = DB::table(
            'pwp_ipaymu'
        )
            ->where(
                'status',
                'berhasil'
            )

            ->where(
                'created_at',
                '>=',
                $startOfMonth
            )

            ->where(
                'created_at',
                '<',
                $nextMonth
            )

            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | JUMLAH USER YANG DONASI PADA BULAN TERPILIH
        |--------------------------------------------------------------------------
        */

        $totalSpenderUsers = DB::table(
            'pwp_ipaymu'
        )
            ->where(
                'status',
                'berhasil'
            )

            ->where(
                'created_at',
                '>=',
                $startOfMonth
            )

            ->where(
                'created_at',
                '<',
                $nextMonth
            )

            ->whereNotNull('user_id')

            ->distinct()

            ->count('user_id');

        /*
        |--------------------------------------------------------------------------
        | NAMA PERIODE
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | April 2026
        |
        */

        $periodLabel = Carbon::create(
            $selectedYear,
            $selectedMonth,
            1
        )
            ->locale(app()->getLocale())
            ->translatedFormat('F Y');

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'front.ranking.topspendercoin',
            [

                /*
                 * Ranking
                 */
                'topSpenders' =>
                    $topSpenders,

                /*
                 * Total rupiah bulan ini
                 */
                'totalMoneyAllSpenders' =>
                    $totalMoneyAllSpenders,

                /*
                 * Total coin bulan ini
                 */
                'totalCoinAllSpenders' =>
                    $totalCoinAllSpenders,

                /*
                 * Jumlah spender bulan ini
                 */
                'totalSpenderUsers' =>
                    $totalSpenderUsers,

                /*
                 * Bulan terpilih
                 */
                'selectedMonth' =>
                    $selectedMonth,

                /*
                 * Tahun terpilih
                 */
                'selectedYear' =>
                    $selectedYear,

                /*
                 * Nama periode
                 */
                'periodLabel' =>
                    $periodLabel,

                /*
                 * Kompatibel Blade lama
                 */
                'bulan' =>
                    $selectedMonth,

                'tahun' =>
                    $selectedYear,
            ]
        );
    }

    /**
     * Export Top Spender ke Excel.
     */
    public function exportExcel()
    {
        return Excel::download(
            new TopSpenderExport,
            'top_spender_coin.xlsx'
        );
    }
}