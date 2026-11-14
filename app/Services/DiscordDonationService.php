<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordDonationService
{
    public static function send(
        string $donorName,
        string $streamerCode,
        float|int $amount,
        ?string $transactionCode = null
    ): bool {
        $webhook = config('ipaymu.discord_webhook_url') ?: config('discord.donation_webhook');

        if (empty($webhook)) {
            Log::warning('Discord webhook belum dikonfigurasi.');

            return false;
        }

        try {

            $description = 'Terima kasih atas dukungan untuk **EternaPW**!';

            $template = config('ipaymu.discord_message');

            if (!empty($template)) {
                $description = str_replace(
                    ['{player_name}', '{amount}', '{streamer}', '{reference_id}', '{payment_method}'],
                    [
                        $donorName ?: '-',
                        number_format($amount, 0, ',', '.'),
                        $streamerCode ?: '-',
                        $transactionCode ?: '-',
                        'iPaymu',
                    ],
                    $template
                );
            }

            $payload = [
                'username' => 'EternaPW Donation',

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
                                'name' => 'Kode Transaksi',
                                'value' => $transactionCode ?: '-',
                                'inline' => false,
                            ],
                        ],

                        'footer' => [
                            'text' => 'EternaPW Donation System',
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