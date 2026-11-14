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
        $webhook = config('discord.donation_webhook');

        if (empty($webhook)) {
            Log::warning('Discord webhook belum dikonfigurasi.');

            return false;
        }

        try {

            $payload = [
                'username' => 'EternaPW Donation',

                'embeds' => [
                    [
                        'title' => 'DONASI BARU',

                        'description' =>
                            'Terima kasih atas dukungan untuk **EternaPW**!',

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