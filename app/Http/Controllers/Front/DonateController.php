<?php

/*
 * @author Harris Marfel <hrace009@gmail.com>
 * @link https://youtube.com/c/hrace009
 * @copyright Copyright (c) 2022.
 */

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\BanklogRequest;
use App\Mail\BankTransfer;
use App\Models\BankLog;
use App\Models\IpaymuLog;
use App\Models\Paymentwall;
use App\Models\Paypal;
use App\Models\ServiceLog;
use App\Models\ShopLog;
use App\Models\User;
use App\Services\IpaymuBroadcastService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use iPaymu\iPaymu;
use Omnipay\Omnipay;
use Paymentwall_Config;
use Paymentwall_Widget;

class DonateController extends Controller
{
    protected $gateway;

    protected $ipaymu;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');

        $this->gateway->initialize([
            'clientId' => config(
                'pw-config.payment.paypal.client_id'
            ),

            'secret' => config(
                'pw-config.payment.paypal.secret'
            ),

            'testMode' => config(
                'pw-config.payment.paypal.sandbox'
            ),
        ]);


        if (!config('ipaymu.sandbox')) {
            $production = true;
        } else {
            $production = false;
        }


        if (
            config('ipaymu.key')
            ||
            config('ipaymu.va')
        ) {

            $this->ipaymu = new iPaymu(
                config('ipaymu.key'),
                config('ipaymu.va'),
                $production
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAYPAL INDEX
    |--------------------------------------------------------------------------
    */

    public function getPaypalIndex()
    {
        return view(
            'front.donate.paypal.index'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PAYPAL SUBMIT
    |--------------------------------------------------------------------------
    */

    public function postPaypalSubmit(Request $request)
    {
        $transaction = $this->gateway->purchase([
            'amount' => number_format(
                $request->dollars,
                0
            ),

            'currency' => config(
                'pw-config.payment.paypal.currency'
            ),

            'description' => __(
                'donate.paypal.description',
                [
                    'amount' => $request->tokens,

                    'currency' => config(
                        'pw-config.currency_name'
                    ),
                ]
            ),

            'returnUrl' => route(
                'app.donate.paypal.complete'
            ),

            'cancelUrl' => route(
                'app.donate.paypal'
            ),
        ]);


        $response = $transaction->send();


        if ($response->isRedirect()) {

            $response->redirect();

        } else {

            echo $response->getMessage();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAYPAL COMPLETE
    |--------------------------------------------------------------------------
    */

    public function postPaypalComplete(Request $request)
    {
        $complete = $this->gateway->completePurchase([
            'transactionReference' => $request->paymentId,

            'payerId' => $request->PayerID,
        ]);


        $response = $complete->send();

        $data = $response->getData();


        if ($data['state'] === 'approved') {

            $user = Auth::user();

            $amount = round(
                $data['transactions'][0]['amount']['total']
            );


            $paymentAmount = config(
                'pw-config.payment.paypal.double'
            )
                ? (
                    $amount
                    *
                    config(
                        'pw-config.payment.paypal.currency_per'
                    )
                ) * 2

                : $amount
                    *
                    config(
                        'pw-config.payment.paypal.currency_per'
                    );


            Paypal::create([
                'user_id' => $user->ID,

                'transaction_id' => $data['id'],

                'amount' => $paymentAmount,

                'money' => $amount,
            ]);


            if (
                config(
                    'pw-config.payment.paypal.bonusess'
                )
            ) {

                if (
                    $amount >= config(
                        'pw-config.payment.paypal.mingetbonus'
                    )
                ) {

                    $bonus = $paymentAmount
                        *
                        (
                            config(
                                'pw-config.payment.paypal.bonusess'
                            ) / 100
                        );

                } else {

                    $bonus = 0;
                }

            } else {

                $bonus = 0;
            }


            $user->money =
                $user->money
                +
                (
                    $paymentAmount
                    +
                    $bonus
                );


            $user->save();
        }


        return redirect()
            ->route(
                'app.donate.paypal'
            )
            ->with(
                'success',
                __('donate.paypal.success')
            );
    }


    /*
    |--------------------------------------------------------------------------
    | BANK INDEX
    |--------------------------------------------------------------------------
    */

    public function getBankIndex()
    {
        return view(
            'front.donate.bank.index'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BANK SUBMIT
    |--------------------------------------------------------------------------
    */

    public function postBank(BanklogRequest $request)
    {
        $input = $request->all();


        BankLog::create(
            $input
        );


        $bank = [
            'fullname' => $input['fullname'],

            'banknum' => $input['banknum'],

            'loginid' => $input['loginid'],

            'gameID' => $input['gameID'],

            'email' => $input['email'],

            'phone' => $input['phone'],

            'type' => $input['type'],

            'bankname' => $input['bankname'],

            'amount' => $input['amount'],
        ];


        Mail::to(
            env('MAIL_GM')
        )
            ->locale(
                Auth::user()->language
            )
            ->send(
                new BankTransfer(
                    $bank
                )
            );


        return redirect(
            route(
                'app.donate.bank'
            )
        )
            ->with(
                'success',
                __(
                    'donate.guide.bank.form.success'
                )
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENTWALL INDEX
    |--------------------------------------------------------------------------
    */

    public function getPaymentwallIndex()
    {
        Paymentwall_Config::getInstance()->set([
            'api_type' => Paymentwall_Config::API_VC,

            'public_key' => config(
                'pw-config.payment.paymentwall.project_key'
            ),

            'private_key' => config(
                'pw-config.payment.paymentwall.secret_key'
            ),
        ]);


        $userId = Auth::user()->ID;

        $userEmail = Auth::user()->email;

        $userRegDate = Auth::user()->created_at;

        $userLastUpdate = Auth::user()->last_update;

        $userName = Auth::user()->name;

        $widgetCode = config(
            'pw-config.payment.paymentwall.widget_code'
        );


        $products = [];


        $extraParams = [
            'email' => $userEmail,

            'history[registration_date]' => $userRegDate,

            'customer[username]' => $userName,

            'history[membership_date]' => $userLastUpdate,
        ];


        $PWWidget = new Paymentwall_Widget(
            $userId,
            $widgetCode,
            $products,
            $extraParams
        );


        $widget = $PWWidget->getHtmlCode([
            'width' => config(
                'pw-config.payment.paymentwall.widget_width'
            ),

            'height' => config(
                'pw-config.payment.paymentwall.widget_high'
            ),
        ]);


        return view(
            'front.donate.paymenwall.index',
            [
                'widget' => $widget,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DONATION HISTORY
    |--------------------------------------------------------------------------
    */

    public function getHistoryIndex()
    {
        $logbank = BankLog::whereGameid(
            Auth::user()->ID
        )->paginate(10);


        $pws = Paymentwall::whereUserid(
            Auth::user()->ID
        )->paginate(10);


        $ingamelogs = ServiceLog::whereUserid(
            Auth::user()->ID
        )->paginate(10);


        $shoplogs = ShopLog::whereUserid(
            Auth::user()->ID
        )->paginate(10);


        $paypals = Paypal::whereUserId(
            Auth::user()->ID
        )->paginate(10);


        $ipaymu = IpaymuLog::whereUserId(
            Auth::user()->ID
        )
            ->orderByDesc('created_at')
            ->paginate(10);


        $user = new User();


        return view(
            'front.donate.history.index',
            [
                'banks' => $logbank,

                'pws' => $pws,

                'user' => $user,

                'ingamelogs' => $ingamelogs,

                'shoplogs' => $shoplogs,

                'paypals' => $paypals,

                'ipaymu' => $ipaymu,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | IPAYMU INDEX
    |--------------------------------------------------------------------------
    */

    public function getIpaymuIndex()
    {
        return view(
            'front.donate.ipaymu.index'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | IPAYMU SUBMIT
    |--------------------------------------------------------------------------
    */

    public function postIpaymuSubmit(Request $request)
    {
        /*
         * User harus mempunyai nomor telepon.
         */

        if (!Auth::user()->phonenumber) {

            return redirect()
                ->route(
                    'profile.show'
                )
                ->with(
                    'info',
                    __('donate.ipaymu.no_phone')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | URL IPAYMU
        |--------------------------------------------------------------------------
        */

        $this->ipaymu->setURL([
            /*
             * User kembali ke website setelah pembayaran.
             */
            'ureturn' => route(
                'app.donate.ipaymu.complete'
            ),

            /*
             * Callback server-to-server.
             *
             * Inilah yang diproses oleh:
             *
             * Pingback@IpaymuCallback
             */
            'unotify' => route(
                'api.ipaymu'
            ),

            /*
             * User membatalkan pembayaran.
             */
            'ucancel' => route(
                'app.donate.ipaymu'
            ),
        ]);


        /*
        |--------------------------------------------------------------------------
        | BUYER
        |--------------------------------------------------------------------------
        */

        $this->ipaymu->setBuyer([
            'name' => Auth::user()->truename,

            'phone' => Auth::user()->phonenumber,

            'email' => Auth::user()->email,
        ]);


        /*
        |--------------------------------------------------------------------------
        | NOMINAL
        |--------------------------------------------------------------------------
        */

        $dollars = $request->input(
            'dollars'
        );


        $vcAmount =
            $dollars
            *
            config(
                'ipaymu.currency_per'
            );


        /*
        |--------------------------------------------------------------------------
        | CART
        |--------------------------------------------------------------------------
        */

        $this->ipaymu->addCart([
            'product' => [
                config(
                    'pw-config.currency_name'
                )
            ],

            'quantity' => [
                1
            ],

            'price' => [
                $dollars
            ],

            'description' => [
                __(
                    'donate.ipaymu.desc_ipay',
                    [
                        'amount' => $vcAmount,

                        'currency' => config(
                            'pw-config.currency_name'
                        ),

                        'pay' => $dollars,

                        'loginid' => Auth::user()->ID,
                    ]
                )
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | REFERENCE ID
        |--------------------------------------------------------------------------
        */

        $refid =
            config(
                'ipaymu.refid'
            )
            .
            '-'
            .
            Carbon::now()->timestamp;


        $paymentData = [
            'referenceId' => $refid,
        ];


        /*
        |--------------------------------------------------------------------------
        | CREATE PAYMENT IPAYMU
        |--------------------------------------------------------------------------
        */

        $redirectPayment =
            $this->ipaymu->redirectPayment(
                $paymentData
            );


        /*
        |--------------------------------------------------------------------------
        | BONUS MINIMUM GET
        |--------------------------------------------------------------------------
        */

        $bonusMinget = 0;


        if (
            config(
                'ipaymu.bonusess'
            )
            &&
            $vcAmount >= config(
                'ipaymu.mingetbonus'
            )
        ) {

            $bonusMinget =
                $vcAmount
                *
                (
                    config(
                        'ipaymu.bonusess'
                    ) / 100
                );
        }


        /*
        |--------------------------------------------------------------------------
        | BONUS PROMO
        |--------------------------------------------------------------------------
        */

        $bonusPromo = 0;


        $inputPromoCode =
            $request->input(
                'promo_code'
            );


        if (
            $inputPromoCode
            &&
            $inputPromoCode !== config(
                'ipaymu.promo_code'
            )
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    __('donate.ipaymu.invalid_promo_code')
                );
        }


        if ($inputPromoCode) {

            $bonusPromo =
                $vcAmount
                *
                (
                    config(
                        'ipaymu.promo_bonus_percent'
                    ) / 100
                );
        }


        /*
        |--------------------------------------------------------------------------
        | BONUS GUILD
        |--------------------------------------------------------------------------
        */

        $bonusGuild = 0;


        $inputGuildCode =
            $request->input(
                'guild_code'
            );


        if ($inputGuildCode) {

            $guilds = config(
                'ipaymu.guild'
            );


            $validGuild = collect(
                $guilds
            )->firstWhere(
                'code_guild',
                $inputGuildCode
            );


            if ($validGuild) {

                $bonusGuild =
                    $vcAmount
                    *
                    (
                        $validGuild[
                            'guild_bonus_percent'
                        ] / 100
                    );

            } else {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        __('donate.ipaymu.invalid_guild_code')
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | BONUS STREAMER
        |--------------------------------------------------------------------------
        */

        $bonusStreamer = 0;

        $streamerId = null;


        if (
            $request->input(
                'code_type'
            ) === 'streamer'
        ) {

            $inputStreamerCode =
                strtolower(
                    trim(
                        $request->input(
                            'streamer_code'
                        )
                    )
                );


            $streamers = config(
                'ipaymu.streamers'
            );


            $validStreamer = collect(
                $streamers
            )->first(
                function ($streamer) use (
                    $inputStreamerCode
                ) {

                    return strtolower(
                        trim(
                            $streamer[
                                'streamer_code'
                            ]
                        )
                    )
                    ===
                    $inputStreamerCode;
                }
            );


            if ($validStreamer) {

                $bonusStreamer =
                    $vcAmount
                    *
                    (
                        $validStreamer[
                            'streamer_bonus_percent'
                        ] / 100
                    );


                $streamerId =
                    $validStreamer[
                        'id'
                    ];


                \Log::info(
                    'Streamer Processing',
                    [
                        'code_type' =>
                            $request->input(
                                'code_type'
                            ),

                        'streamer_code' =>
                            $request->input(
                                'streamer_code'
                            ),

                        'resolved_id' =>
                            $streamerId,

                        'bonus_calculated' =>
                            $bonusStreamer,
                    ]
                );

            } else {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        __('donate.ipaymu.invalid_streamer_code')
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL BONUS
        |--------------------------------------------------------------------------
        */

        $totalBonus =
            $bonusMinget
            +
            $bonusPromo
            +
            $bonusGuild
            +
            $bonusStreamer;


        /*
        |--------------------------------------------------------------------------
        | SESSION ID IPAYMU
        |--------------------------------------------------------------------------
        */

        $sid =
            $redirectPayment[
                'Data'
            ][
                'SessionID'
            ]
            ??
            null;


        if (!$sid) {

            \Log::error(
                'Gagal mendapatkan SessionID dari redirectPayment.',
                [
                    'redirectData' =>
                        $redirectPayment,
                ]
            );


            return redirect()
                ->back()
                ->with(
                    'error',
                    'Gagal memproses pembayaran. Silakan coba lagi.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN SID KE SESSION
        |--------------------------------------------------------------------------
        */

        session([
            'ipaymu_sid' => $sid,
        ]);


        /*
        |--------------------------------------------------------------------------
        | SIMPAN TRANSAKSI PENDING
        |--------------------------------------------------------------------------
        */

        IpaymuLog::create([
            'reference_id' =>
                $refid,

            'user_id' =>
                Auth::user()->ID,

            'streamer_id' =>
                $streamerId,

            'streamer_bonus' =>
                (int) round(
                    $bonusStreamer
                ),

            'amount' =>
                $vcAmount
                +
                $totalBonus,

            'money' =>
                $dollars,

            'status' =>
                'pending',

            'status_code' =>
                '0',

            'sid' =>
                $sid,
        ]);


        /*
        |--------------------------------------------------------------------------
        | REDIRECT KE IPAYMU
        |--------------------------------------------------------------------------
        */

        return redirect(
            $redirectPayment[
                'Data'
            ][
                'Url'
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | IPAYMU COMPLETE / RETURN URL
    |--------------------------------------------------------------------------
    |
    | Fungsi ini menjadi fallback apabila callback iPaymu belum datang.
    |
    | Jika callback Pingback sudah memproses transaksi, fungsi ini akan
    | melihat status "berhasil" dan tidak menambah coin lagi.
    |
    */

    public function postIpaymuComplete(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL SID
        |--------------------------------------------------------------------------
        */

        $sid =
            $request->get(
                'sid'
            )
            ??
            session(
                'ipaymu_sid'
            );


        if (!$sid) {

            \Log::error(
                'Callback/return iPaymu tanpa SID.'
            );


            return redirect()
                ->route(
                    'app.donate.ipaymu'
                )
                ->with(
                    'error',
                    'Transaksi tidak valid.'
                );
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | DATABASE TRANSACTION
            |--------------------------------------------------------------------------
            */

            $processedLog = null;

            DB::transaction(
                function () use (
                    $sid,
                    &$processedLog
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | CARI TRANSAKSI
                    |--------------------------------------------------------------------------
                    */

                    $log = IpaymuLog::where(
                        'sid',
                        $sid
                    )
                        ->lockForUpdate()
                        ->first();


                    if (!$log) {

                        \Log::warning(
                            'Transaksi iPaymu tidak ditemukan.',
                            [
                                'sid' => $sid,
                            ]
                        );


                        throw new \Exception(
                            'Transaksi tidak ditemukan.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SUDAH BERHASIL
                    |--------------------------------------------------------------------------
                    |
                    | Jika Pingback callback sudah lebih dulu memproses,
                    | jangan tambahkan coin lagi.
                    |
                    */

                    if (
                        $log->status === 'completed'
                        ||
                        (
                            $log->status === 'berhasil'
                            &&
                            (string) $log->status_code === '1'
                        )
                    ) {

                        \Log::info(
                            'Transaksi iPaymu sudah diproses sebelumnya.',
                            [
                                'sid' => $sid,

                                'reference_id' =>
                                    $log->reference_id,
                            ]
                        );


                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CEK STATUS KE IPAYMU
                    |--------------------------------------------------------------------------
                    */

                    $checkStatus =
                        $this->ipaymu->checkTransaction([
                            'transactionId' =>
                                $log->reference_id,
                        ]);


                    $status =
                        strtolower(
                            trim(
                                (string) (
                                    $checkStatus[
                                        'Data'
                                    ][
                                        'Status'
                                    ]
                                    ??
                                    ''
                                )
                            )
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | BELUM BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $status !== 'berhasil'
                    ) {

                        \Log::warning(
                            'Pembayaran belum selesai di iPaymu.',
                            [
                                'reference_id' =>
                                    $log->reference_id,

                                'status_response' =>
                                    $checkStatus,
                            ]
                        );


                        throw new \Exception(
                            'Status pembayaran belum berhasil.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | USER DONATUR
                    |--------------------------------------------------------------------------
                    */

                    $user = User::find(
                        $log->user_id
                    );


                    if (!$user) {

                        throw new \Exception(
                            'User donatur tidak ditemukan.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | BONUS STREAMER
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $log->streamer_id
                        &&
                        $log->streamer_bonus > 0
                    ) {

                        $streamer = User::find(
                            $log->streamer_id
                        );


                        if ($streamer) {

                            $bonusCoin =
                                (int) round(
                                    $log->streamer_bonus
                                );


                            $streamer->increment(
                                'money',
                                $bonusCoin
                            );


                            \Log::info(
                                'Coin bonus ditambahkan ke streamer.',
                                [
                                    'streamer_id' =>
                                        $streamer->ID,

                                    'bonus_coin' =>
                                        $bonusCoin,

                                    'reference_id' =>
                                        $log->reference_id,
                                ]
                            );

                        } else {

                            \Log::warning(
                                'Streamer user tidak ditemukan.',
                                [
                                    'streamer_id' =>
                                        $log->streamer_id,

                                    'reference_id' =>
                                        $log->reference_id,
                                ]
                            );
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | TAMBAH COIN DONATUR
                    |--------------------------------------------------------------------------
                    */

                    $coinAmount =
                        (float) $log->amount;


                    if (
                        config(
                            'ipaymu.double'
                        )
                    ) {

                        $coinAmount *= 2;
                    }


                    $user->increment(
                        'money',
                        $coinAmount
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE STATUS DATABASE
                    |--------------------------------------------------------------------------
                    */

                    $log->status =
                        'berhasil';


                    $log->status_code =
                        '1';


                    $log->save();


                    /*
                    |--------------------------------------------------------------------------
                    | LOG BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    \Log::info(
                        'Transaksi iPaymu selesai dan berhasil divalidasi.',
                        [
                            'sid' =>
                                $log->sid,

                            'reference_id' =>
                                $log->reference_id,

                            'user_id' =>
                                $log->user_id,

                            'coin' =>
                                $coinAmount,

                            'money' =>
                                $log->money,
                        ]
                    );


                    /*
                     * Tidak ada Discord di sini.
                     *
                     * Discord hanya dikirim oleh:
                     *
                     * php artisan donation:discord
                     *
                     * dan command tersebut hanya membaca:
                     *
                     * status = berhasil
                     * status_code = 1
                     */

                    $processedLog = $log;
                }
            );


            /*
             * Broadcast in-game hanya untuk transaksi yang baru berhasil,
             * setelah database commit dan setelah response dikirim
             * (socket game server bisa blocking tanpa timeout).
             */
            if ($processedLog) {
                app()->terminating(fn () => IpaymuBroadcastService::send($processedLog));
            }


            /*
            |--------------------------------------------------------------------------
            | HAPUS SESSION
            |--------------------------------------------------------------------------
            */

            session()->forget([
                'ipaymu_sid',

                'ipaymu_streamer_id',

                'ipaymu_streamer_bonus',
            ]);


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'app.donate.ipaymu'
                )
                ->with(
                    'success',
                    __('donate.ipaymu.success')
                );


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

            \Log::error(
                'Gagal menyelesaikan transaksi iPaymu.',
                [
                    'sid' =>
                        $sid,

                    'message' =>
                        $e->getMessage(),
                ]
            );


            return redirect()
                ->route(
                    'app.donate.ipaymu'
                )
                ->with(
                    'error',
                    'Transaksi belum dapat diproses. Jika Anda sudah membayar, mohon tunggu beberapa saat atau hubungi admin.'
                );
        }
    }
}