<?php

/*
 * @author Harris Marfel <hrace009@gmail.com>
 * @link https://youtube.com/c/hrace009
 * @copyright Copyright (c) 2022.
 */

namespace App\Http\Controllers;

use App\Models\IpaymuLog;
use App\Models\Paymentwall;
use App\Models\User;
use App\Services\IpaymuBroadcastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Paymentwall_Config;
use Paymentwall_Pingback;

class Pingback extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | UPDATE MONEY
    |--------------------------------------------------------------------------
    */

    public function updateMoney($ID, $amount): void
    {
        $user = User::whereId($ID)->firstOrFail();

        $user->increment('money', $amount);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PAYMENTWALL LOG
    |--------------------------------------------------------------------------
    */

    public function createLog($id, $amount, $refid, $type): void
    {
        Paymentwall::create([
            'userID' => $id,
            'amount' => $amount,
            'refid'  => $refid,
            'type'   => $type
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENTWALL CALLBACK
    |--------------------------------------------------------------------------
    */

    public function paymentwall()
    {
        Paymentwall_Config::getInstance()->set([
            'api_type' => Paymentwall_Config::API_VC,

            'public_key' => config(
                'pw-config.payment.paymentwall.project_key'
            ),

            'private_key' => config(
                'pw-config.payment.paymentwall.secret_key'
            )
        ]);


        $pingback = new Paymentwall_Pingback(
            $_GET,
            $_SERVER['REMOTE_ADDR']
        );


        if ($pingback->validate()) {

            $userID = $pingback->getUserId();

            $amount = $pingback->getVirtualCurrencyAmount();

            $refID = $pingback->getReferenceId();

            $type = $pingback->getType();


            /*
             * Payment success
             */
            if ($type === 0) {

                $this->updateMoney(
                    $userID,
                    $amount
                );
            }


            /*
             * Send by customer service
             */
            if ($type === 1) {

                $this->updateMoney(
                    $userID,
                    $amount
                );
            }


            /*
             * Existing behavior
             */
            if ($type === 2) {

                $this->updateMoney(
                    $userID,
                    $amount
                );
            }


            $this->createLog(
                $userID,
                $amount,
                $refID,
                $type
            );


            return 'OK';
        }


        return $pingback->getErrorSummary();
    }


    /*
    |--------------------------------------------------------------------------
    | IPAYMU CALLBACK
    |--------------------------------------------------------------------------
    */

    public function IpaymuCallback(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | DATA DARI IPAYMU
        |--------------------------------------------------------------------------
        */

        $trxId = $request->get('trx_id');


        $status = strtolower(
            trim(
                (string) $request->get('status')
            )
        );


        $statusCode = trim(
            (string) $request->get('status_code')
        );


        $sid = $request->get('sid');


        $referenceId = $request->get(
            'reference_id'
        );


        /*
        |--------------------------------------------------------------------------
        | LOG CALLBACK MASUK
        |--------------------------------------------------------------------------
        */

        Log::info(
            'IPAYMU CALLBACK MASUK',
            [
                'trx_id' => $trxId,

                'status' => $status,

                'status_code' => $statusCode,

                'sid' => $sid,

                'reference_id' => $referenceId,

                'ip' => $request->ip(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SID
        |--------------------------------------------------------------------------
        */

        if (empty($sid)) {

            Log::warning(
                'IPAYMU CALLBACK: SID kosong.',
                [
                    'request' => $request->all(),
                ]
            );


            return response(
                'failed',
                400
            );
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | DATABASE TRANSACTION
            |--------------------------------------------------------------------------
            |
            | lockForUpdate dipakai supaya callback yang datang bersamaan
            | tidak memproses coin dua kali.
            |
            */

            $processedPayment = null;

            $result = DB::transaction(
                function () use (
                    $trxId,
                    $status,
                    $statusCode,
                    $sid,
                    $referenceId,
                    &$processedPayment
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | CARI TRANSAKSI BERDASARKAN SID
                    |--------------------------------------------------------------------------
                    */

                    $payment = IpaymuLog::where(
                        'sid',
                        $sid
                    )
                        ->lockForUpdate()
                        ->first();


                    /*
                    |--------------------------------------------------------------------------
                    | TRANSAKSI TIDAK DITEMUKAN
                    |--------------------------------------------------------------------------
                    */

                    if (!$payment) {

                        Log::warning(
                            'IPAYMU CALLBACK: Transaksi tidak ditemukan.',
                            [
                                'sid' => $sid,

                                'reference_id' => $referenceId,

                                'trx_id' => $trxId,
                            ]
                        );


                        return 'failed';
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN TRX ID IPAYMU
                    |--------------------------------------------------------------------------
                    */

                    if (!empty($trxId)) {

                        $payment->trx_id = $trxId;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PEMBAYARAN BELUM BERHASIL
                    |--------------------------------------------------------------------------
                    |
                    | Kalau status dari iPaymu belum berhasil:
                    |
                    | - jangan tambah coin
                    | - jangan tambah bonus streamer
                    | - simpan trx_id saja
                    |
                    */

                    if (
                        $status !== 'berhasil'
                        ||
                        $statusCode !== '1'
                    ) {

                        $payment->save();


                        Log::info(
                            'IPAYMU CALLBACK: Pembayaran belum berhasil.',
                            [
                                'sid' => $sid,

                                'reference_id' => $payment->reference_id,

                                'trx_id' => $trxId,

                                'status' => $status,

                                'status_code' => $statusCode,
                            ]
                        );


                        return 'success update trx id';
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | TRANSAKSI SUDAH PERNAH BERHASIL
                    |--------------------------------------------------------------------------
                    |
                    | iPaymu bisa mengirim callback lebih dari satu kali.
                    |
                    | Kalau transaksi sudah berhasil:
                    |
                    | - jangan tambah coin lagi
                    | - jangan tambah bonus streamer lagi
                    |
                    */

                    if (
                        $payment->status === 'berhasil'
                        &&
                        (string) $payment->status_code === '1'
                    ) {

                        /*
                         * Tetap simpan trx_id jika sebelumnya kosong.
                         */
                        $payment->save();


                        Log::info(
                            'IPAYMU CALLBACK: Transaksi sudah berhasil sebelumnya.',
                            [
                                'sid' => $sid,

                                'reference_id' => $payment->reference_id,

                                'trx_id' => $payment->trx_id,
                            ]
                        );


                        return 'success';
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CARI USER DONATUR
                    |--------------------------------------------------------------------------
                    */

                    $donor = User::find(
                        $payment->user_id
                    );


                    if (!$donor) {

                        throw new \Exception(
                            'User donatur tidak ditemukan. User ID: '
                            .
                            $payment->user_id
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG COIN DONATUR
                    |--------------------------------------------------------------------------
                    */

                    $coinAmount = (float) $payment->amount;


                    if (config('ipaymu.double')) {

                        $coinAmount *= 2;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | TAMBAH COIN DONATUR
                    |--------------------------------------------------------------------------
                    */

                    $donor->increment(
                        'money',
                        $coinAmount
                    );


                    Log::info(
                        'IPAYMU: Coin ditambahkan ke donatur.',
                        [
                            'user_id' => $donor->ID,

                            'coin' => $coinAmount,

                            'sid' => $sid,

                            'reference_id' => $payment->reference_id,
                        ]
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | BONUS STREAMER
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !empty($payment->streamer_id)
                        &&
                        (float) $payment->streamer_bonus > 0
                    ) {

                        $streamer = User::find(
                            $payment->streamer_id
                        );


                        if ($streamer) {

                            $bonusCoin = (int) round(
                                (float) $payment->streamer_bonus
                            );


                            $streamer->increment(
                                'money',
                                $bonusCoin
                            );


                            Log::info(
                                'IPAYMU: Bonus coin streamer ditambahkan.',
                                [
                                    'streamer_id' => $streamer->ID,

                                    'bonus_coin' => $bonusCoin,

                                    'sid' => $sid,

                                    'reference_id' => $payment->reference_id,
                                ]
                            );

                        } else {

                            Log::warning(
                                'IPAYMU: User streamer tidak ditemukan.',
                                [
                                    'streamer_id' => $payment->streamer_id,

                                    'sid' => $sid,

                                    'reference_id' => $payment->reference_id,
                                ]
                            );
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE STATUS TRANSAKSI
                    |--------------------------------------------------------------------------
                    */

                    DB::table('pwp_ipaymu')
    ->where('id', $payment->id)
    ->update([
        'trx_id' => $trxId,
        'updated_at' => now(),
    ]);


                    $affected = DB::table('pwp_ipaymu')
    ->where('id', $payment->id)
    ->update([
        'trx_id' => $trxId,
        'status' => 'berhasil',
        'status_code' => '1',
        'updated_at' => now(),
    ]);

Log::info('IPAYMU DATABASE UPDATE', [
    'id' => $payment->id,
    'sid' => $sid,
    'trx_id' => $trxId,
    'reference_id' => $payment->reference_id,
    'affected_rows' => $affected,
]);


                    /*
                    |--------------------------------------------------------------------------
                    | LOG BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    Log::info(
                        'IPAYMU: Transaksi berhasil diproses.',
                        [
                            'sid' => $sid,

                            'trx_id' => $trxId,

                            'reference_id' => $payment->reference_id,

                            'user_id' => $payment->user_id,

                            'coin' => $coinAmount,

                            'money' => $payment->money,

                            'streamer_id' => $payment->streamer_id,
                        ]
                    );


                    /*
                     * Discord TIDAK dikirim dari sini.
                     *
                     * Discord akan dikirim oleh:
                     *
                     * php artisan donation:discord
                     *
                     * dengan membaca database:
                     *
                     * status = berhasil
                     * status_code = 1
                     */

                    $processedPayment = $payment;

                    return 'success';
                }
            );


            /*
             * Broadcast in-game hanya untuk transaksi yang baru berhasil,
             * setelah database commit dan setelah response dikirim
             * (socket game server bisa blocking tanpa timeout).
             */
            if ($processedPayment) {
                app()->terminating(fn () => IpaymuBroadcastService::send($processedPayment));
            }


            /*
            |--------------------------------------------------------------------------
            | RESPONSE KE IPAYMU
            |--------------------------------------------------------------------------
            */

            return $result;


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | CALLBACK ERROR
            |--------------------------------------------------------------------------
            */

            Log::error(
                'IPAYMU CALLBACK ERROR',
                [
                    'sid' => $sid,

                    'trx_id' => $trxId,

                    'reference_id' => $referenceId,

                    'status' => $status,

                    'status_code' => $statusCode,

                    'error' => $e->getMessage(),

                    'line' => $e->getLine(),

                    'file' => $e->getFile(),
                ]
            );


            return response(
                'failed',
                500
            );
        }
    }
}