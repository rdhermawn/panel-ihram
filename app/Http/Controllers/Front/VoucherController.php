<?php



/*
 * @author Harris Marfel <hrace009@gmail.com>
 * @link https://youtube.com/c/hrace009
 * @copyright Copyright (c) 2022.
 */

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use hrace009\PerfectWorldAPI\API;


class VoucherController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('selected.character', ['only' => 'postRedem']);
    }

    public function getIndex()
    {
        $voucher_logs = Auth::user()->voucher_logs()->paginate();
        return view('front.voucher.index', [
            'voucher_logs' => $voucher_logs,
        ]);
    }

    public function postRedem(Request $request)
{
    $voucher = Voucher::whereCode($request->code)->first();
    $user = Auth::user();
    $characterId = $user->characterId();

    if (!$characterId) {
        return redirect()->back()->with('error', 'voucher.selected_character');
    }

    if (!$voucher) {
        return redirect()->back()->with('error', __('voucher.code_not_found'));
    }

    $ip = $request->ip();

    // CEK: IP address sudah pernah digunakan untuk redeem voucher ini?
    $ipHasRedeemed = VoucherLog::where('voucher_id', $voucher->id)
        ->where('ip_address', $ip)
        ->exists();

    if ($ipHasRedeemed) {
        return redirect()->back()->with('error', __('voucher.ip_already_redeemed'));
    }

    // CEK: User sudah pernah redeem voucher ini?
    if (VoucherLog::redeemed($voucher->id, $user->ID)->exists()) {
        return redirect()->back()->with('error', __('voucher.already_redeemed'));
    }

    // CEK: Apakah kuota voucher sudah habis?
    if ($voucher->times_redeemed >= $voucher->max_redeem) {
        return redirect()->back()->with('error', __('voucher.max_redeem_reached'));
    }

    // REDEEM
    VoucherLog::create([
        'voucher_id' => $voucher->id,
        'user_id' => $user->ID,
        'ip_address' => $ip,
    ]);

    $voucher->increment('times_redeemed');
    $user->increment('money', $voucher->amount);

    // KIRIM ITEM JIKA ADA
    if ($voucher->item_id) {
        $api = new API();

        $mail = [
            'title' => __('voucher.mail_item.title', ['name' => config('app.name')]),
            'message' => __('voucher.mail_item.message', [
                'name' => $voucher->name ?? $voucher->code,
                'count' => $voucher->count ?? 1,
                'staff' => config('app.name')
            ]),
            'money' => 0,
            'item' => [
                'id' => $voucher->item_id, 
                'pos' => 0,
                'count' => $voucher->count ?? 1,
                'max_count' => $voucher->max_count ?? 0,
                'data' => $voucher->octet ?? '',
                'proctype' => $voucher->protection_type ?? 0,
                'expire_date' => $voucher->expire_date ?? '',
                'guid1' => 0,
                'guid2' => 0,
                'mask' => $voucher->mask ?? 0,
            ]
        ];

        $api->sendMail($characterId, $mail['title'], $mail['message'], $mail['item'], $mail['money']);
    }

    return redirect()->back()->with('success', __('voucher.successfully_redeemed'));
}


}
