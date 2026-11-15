<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordDonationService
{
    public const DEFAULT_MESSAGE = 'Terima kasih {player_name} atas donasi Rp {amount} untuk **AuraPW**!';

    public static function send(
        string $donorName,
        string $streamerCode,
        float|int $amount,
        ?string $transactionCode = null,
        float|int $coin = 0
    ): bool {
        $webhook = config('ipaymu.discord_webhook_url');

        if (empty($webhook)) {
            Log::warning('Discord webhook belum dikonfigurasi.');

            return false;
        }

        try {

            $template = config('ipaymu.discord_message') ?: self::DEFAULT_MESSAGE;

            $currency = (string) config('pw-config.currency_name');

            $description = str_replace(
                ['{player_name}', '{amount}', '{coin}', '{currency}', '{streamer}', '{reference_id}', '{payment_method}'],
                [
                    $donorName ?: '-',
                    number_format($amount, 0, ',', '.'),
                    number_format($coin, 0, ',', '.'),
                    $currency,
                    $streamerCode ?: '-',
                    $transactionCode ?: '-',
                    'iPaymu',
                ],
                $template
            );

            $payload = [
                'username' => 'AuraPW Donation',

                'embeds' => [
                    [
                        'title' => 'DONASI BARU',

                        'description' => $description,

                        'color' => 3066993,

                        'fields' => [

                            [
                                'name' => 'Donatur',
                                'value' => $donorName ?: '-',
                                'inline' => true,
                            ],

                            [
                                'name' => 'Kode Streamer',
                                'value' => $streamerCode ?: '-',
                                'inline' => true,
                            ],

                            [
                                'name' => 'Jumlah Donasi',
                                'value' => 'Rp ' . number_format(
                                    $amount,
                                    0,
                                    ',',
                                    '.'
                                ),
                                'inline' => false,
                            ],

                            [
                                'name' => 'Jumlah Coin',
                                'value' => number_format($coin, 0, ',', '.') . ' ' . $currency,
                                'inline' => false,
                            ],

                            [
                                'name' => 'Kode Transaksi',
                                'value' => $transactionCode ?: '-',
                                'inline' => false,
                            ],
                        ],

                        'footer' => [
                            'text' => 'AuraPW Donation System',
                        ],

                        'timestamp' => now()->toIso8601String(),
                    ],
                ],
            ];

            if (config('ipaymu.discord_mention_everyone')) {
                $payload['content'] = '@everyone';
                $payload['allowed_mentions'] = [
                    'parse' => ['everyone'],
                ];
            }

            $response = Http::timeout(10)->post(
                $webhook,
                $payload
            );

            if ($response->successful()) {

                Log::info(
                    'Discord donation notification sent.',
                    [
                        'donor' => $donorName,
                        'streamer' => $streamerCode,
                        'amount' => $amount,
                        'coin' => $coin,
                        'transaction' => $transactionCode,
                    ]
                );

                return true;
            }

            Log::error(
                'Discord webhook gagal.',
                [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]
            );

            return false;

        } catch (\Throwable $e) {

            Log::error(
                'Discord donation exception.',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return false;
        }
    }
}