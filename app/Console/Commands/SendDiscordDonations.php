<?php

namespace App\Console\Commands;

use App\Models\IpaymuLog;
use App\Models\User;
use App\Services\DiscordDonationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDiscordDonations extends Command
{
    protected $signature = 'donation:discord {--last= : Kirim ulang transaksi terakhir}';

    protected $description = 'Kirim transaksi iPaymu berhasil ke Discord';

    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | KIRIM ULANG TRANSAKSI TERAKHIR
        |--------------------------------------------------------------------------
        */

        $last = $this->option('last');

        if ($last !== null) {

            $jumlah = (int) $last;

            if ($jumlah < 1) {
                $this->error('Jumlah transaksi harus minimal 1.');
                return 1;
            }

            $transactions = IpaymuLog::where('status', 'berhasil')
                ->where('status_code', '1')
                ->orderByDesc('id')
                ->limit($jumlah)
                ->get()
                ->sortBy('id')
                ->values();

            if ($transactions->isEmpty()) {
                $this->info('Tidak ada transaksi berhasil.');
                return 0;
            }

            $sentCount = 0;

            foreach ($transactions as $transaction) {

                $this->info(
                    'Mengirim ulang transaksi ID ' . $transaction->id
                );

                if ($this->sendDonation($transaction)) {
                    $sentCount++;
                }
            }

            $this->info(
                $sentCount .
                ' transaksi terakhir berhasil dikirim ulang ke Discord.'
            );

            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | FILE PENANDA
        |--------------------------------------------------------------------------
        |
        | Database tidak diubah.
        | Reference ID transaksi yang sudah dikirim disimpan di file ini.
        |
        */

        $stateFile = storage_path(
            'app/discord_donations_sent.json'
        );

        /*
        |--------------------------------------------------------------------------
        | AMBIL TRANSAKSI BERHASIL
        |--------------------------------------------------------------------------
        */

        $transactions = IpaymuLog::where('status', 'berhasil')
    ->where('status_code', '1')
    ->orderByDesc('id')
    ->limit(500)
    ->get()
    ->sortBy('id')
    ->values();


        if ($transactions->isEmpty()) {

            $this->info(
                'Tidak ada transaksi berhasil.'
            );

            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | BACA TRANSAKSI YANG SUDAH DIKIRIM
        |--------------------------------------------------------------------------
        */

        $sent = [];

        $firstRun = !file_exists(
            $stateFile
        );


        if (!$firstRun) {

            $json = file_get_contents(
                $stateFile
            );

            $decoded = json_decode(
                $json,
                true
            );

            if (is_array($decoded)) {
                $sent = $decoded;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FIRST RUN
        |--------------------------------------------------------------------------
        |
        | Pada pertama kali command dijalankan:
        |
        | - transaksi lama tidak akan dispam ke Discord
        | - hanya transaksi berhasil PALING BARU yang dikirim
        |
        */

        if ($firstRun) {

            $latest = $transactions->last();

            if (!$latest) {
                return 0;
            }

            $this->info(
                'First run. Mengirim transaksi berhasil terbaru.'
            );


            /*
             * Tandai transaksi lama sebagai sudah dibaca.
             */
            foreach ($transactions as $transaction) {

                $key = $this->transactionKey(
                    $transaction
                );

                if ($key) {
                    $sent[] = $key;
                }
            }


            /*
             * Hapus latest dulu.
             *
             * Kalau pengiriman gagal,
             * transaksi terbaru masih bisa dicoba lagi.
             */
            $latestKey = $this->transactionKey(
                $latest
            );

            if ($latestKey) {

                $sent = array_values(
                    array_filter(
                        $sent,
                        function ($item) use ($latestKey) {
                            return $item !== $latestKey;
                        }
                    )
                );
            }


            $this->saveState(
                $stateFile,
                $sent
            );


            /*
             * Kirim transaksi terbaru.
             */
            if ($this->sendDonation($latest)) {

                if ($latestKey) {
                    $sent[] = $latestKey;
                }

                $this->saveState(
                    $stateFile,
                    $sent
                );
            }


            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | PROSES TRANSAKSI BARU
        |--------------------------------------------------------------------------
        */

        $sentCount = 0;


        foreach ($transactions as $transaction) {

            $key = $this->transactionKey(
                $transaction
            );


            if (!$key) {

                Log::warning(
                    'DISCORD DONATION: Transaksi tidak mempunyai identifier.',
                    [
                        'id' => $transaction->id ?? null,
                    ]
                );

                continue;
            }


            /*
             * Sudah pernah dikirim.
             */
            if (in_array($key, $sent, true)) {
                continue;
            }


            /*
             * Kirim Discord.
             */
            if (
                $this->sendDonation(
                    $transaction
                )
            ) {

                $sent[] = $key;

                $sent = array_values(
                    array_unique($sent)
                );


                $this->saveState(
                    $stateFile,
                    $sent
                );


                $sentCount++;
            }
        }


        if ($sentCount > 0) {

            $this->info(
                $sentCount .
                ' transaksi berhasil dikirim ke Discord.'
            );

        } else {

            $this->info(
                'Tidak ada transaksi baru untuk Discord.'
            );
        }


        return 0;
    }


    /*
    |--------------------------------------------------------------------------
    | KIRIM DONASI
    |--------------------------------------------------------------------------
    */

    private function sendDonation($transaction): bool
    {
        /*
         * Discord dimatikan dari Admin > Donate > iPaymu, atau webhook belum diisi.
         * Transaksi tetap ditandai sudah diproses supaya tidak
         * dikirim massal saat Discord diaktifkan kembali.
         */
        if (
            !config('ipaymu.discord_enabled')
            ||
            empty(config('ipaymu.discord_webhook_url'))
        ) {
            return true;
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | DONATUR
            |--------------------------------------------------------------------------
            */

            $user = User::find(
    $transaction->user_id
);

if ($user) {

    if (!empty(trim($user->truename ?? ''))) {

        $donorName = $user->truename;

    } elseif (!empty(trim($user->name ?? ''))) {

        $donorName = $user->name;

    } else {

        $donorName = 'User #' . $transaction->user_id;

    }

} else {

    $donorName = 'User #' . $transaction->user_id;

}


            /*
            |--------------------------------------------------------------------------
            | STREAMER
            |--------------------------------------------------------------------------
            */

            $streamerCode = '-';


            if (
                !empty(
                    $transaction->streamer_id
                )
            ) {

                $streamer = \App\Models\Streamer::where(
                    'user_id',
                    $transaction->streamer_id
                )->first();


                if ($streamer) {

                    $streamerCode = $streamer->code;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | NOMINAL
            |--------------------------------------------------------------------------
            */

            $amount = $transaction->money
                ?? $transaction->amount
                ?? 0;


            /*
            |--------------------------------------------------------------------------
            | REFERENCE ID
            |--------------------------------------------------------------------------
            */

            $referenceId =
                $transaction->reference_id
                ??
                $transaction->sid
                ??
                $transaction->trx_id
                ??
                '-';


            /*
            |--------------------------------------------------------------------------
            | KIRIM DISCORD
            |--------------------------------------------------------------------------
            */

            $sent = DiscordDonationService::send(
                $donorName,
                $streamerCode,
                $amount,
                $referenceId
            );


            /*
             * Webhook gagal: jangan tandai terkirim,
             * dicoba lagi pada jadwal berikutnya.
             */
            if (!$sent) {

                $this->error(
                    'Discord gagal: ' .
                    $referenceId
                );

                return false;
            }


            Log::info(
                'DATABASE DONATION: Discord terkirim.',
                [
                    'donor' => $donorName,
                    'streamer' => $streamerCode,
                    'amount' => $amount,
                    'reference_id' => $referenceId,
                ]
            );


            $this->info(
                'Discord: ' .
                $referenceId .
                ' - ' .
                $donorName
            );


            return true;

        } catch (\Throwable $e) {

            Log::error(
                'DATABASE DONATION: Discord gagal.',
                [
                    'reference_id' =>
                        $transaction->reference_id
                        ?? null,

                    'error' =>
                        $e->getMessage(),
                ]
            );


            $this->error(
                'Discord gagal: ' .
                $e->getMessage()
            );


            return false;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UNIQUE TRANSACTION KEY
    |--------------------------------------------------------------------------
    */

    private function transactionKey($transaction): ?string
    {
        if (
            !empty(
                $transaction->reference_id
            )
        ) {

            return 'REF:' .
                $transaction->reference_id;
        }


        if (
            !empty(
                $transaction->sid
            )
        ) {

            return 'SID:' .
                $transaction->sid;
        }


        if (
            !empty(
                $transaction->trx_id
            )
        ) {

            return 'TRX:' .
                $transaction->trx_id;
        }


        if (
            !empty(
                $transaction->id
            )
        ) {

            return 'ID:' .
                $transaction->id;
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN STATE
    |--------------------------------------------------------------------------
    */

    private function saveState(
        string $file,
        array $sent
    ): void {

        /*
         * Simpan maksimal 5000 transaksi terakhir.
         */
        if (count($sent) > 5000) {

            $sent = array_slice(
                $sent,
                -5000
            );
        }


        file_put_contents(
            $file,
            json_encode(
                array_values(
                    array_unique($sent)
                ),
                JSON_PRETTY_PRINT
                |
                JSON_UNESCAPED_SLASHES
            ),
            LOCK_EX
        );
    }
}
