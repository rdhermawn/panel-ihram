<?php

namespace App\Services;

use App\Models\IpaymuLog;
use App\Models\User;
use hrace009\PerfectWorldAPI\API;
use Illuminate\Support\Facades\Log;

class IpaymuBroadcastService
{
    public const DEFAULT_MESSAGE = '{player_name} telah melakukan top up Rp {amount} dan mendapatkan {coin} {currency}!';

    /**
     * Kirim broadcast in-game untuk transaksi iPaymu yang baru berhasil.
     * Tidak pernah melempar exception supaya proses pembayaran tidak terganggu.
     */
    public static function send(IpaymuLog $log): void
    {
        if (!config('ipaymu.broadcast_enabled')) {
            return;
        }

        try {
            $user = User::find($log->user_id);

            $playerName = trim($user->truename ?? '') ?: (trim($user->name ?? '') ?: 'User #' . $log->user_id);

            $coin = (float) $log->amount;
            if (config('ipaymu.double')) {
                $coin *= 2;
            }

            $template = config('ipaymu.broadcast_message') ?: self::DEFAULT_MESSAGE;

            $message = str_replace(
                ['{player_name}', '{amount}', '{coin}', '{currency}', '{payment_method}'],
                [
                    $playerName,
                    number_format((float) $log->money, 0, ',', '.'),
                    number_format($coin, 0, ',', '.'),
                    config('pw-config.currency_name'),
                    'iPaymu',
                ],
                $template
            );

            $channel = (int) (config('ipaymu.broadcast_channel') ?: 9);

            $api = new API();

            if (!$api->online) {
                Log::warning('IPAYMU BROADCAST: Server offline, broadcast dilewati.', [
                    'reference_id' => $log->reference_id,
                ]);

                return;
            }

            $api->WorldChat(0, $message, $channel);

            Log::info('IPAYMU BROADCAST: Terkirim.', [
                'reference_id' => $log->reference_id,
                'channel' => $channel,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('IPAYMU BROADCAST: Gagal.', [
                'reference_id' => $log->reference_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
