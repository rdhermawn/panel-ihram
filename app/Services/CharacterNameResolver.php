<?php

namespace App\Services;

use App\Models\User;
use hrace009\PerfectWorldAPI\API;
use Illuminate\Support\Facades\Log;

class CharacterNameResolver
{
    /**
     * Nama karakter untuk sebuah akun. Prioritas: karakter yang dipilih di sesi
     * (jika akun yang sama), karakter level tertinggi dari server game,
     * dan fallback ke nama akun jika tidak ada karakter.
     */
    public static function forUser(?int $userId): string
    {
        $user = $userId ? User::find($userId) : null;

        if (!$user) {
            return 'User #' . $userId;
        }

        try {
            if (auth()->check() && (int) auth()->id() === (int) $user->ID) {
                $name = trim((string) session('character_name'));
                if ($name !== '') {
                    return $name;
                }
            }
        } catch (\Throwable $e) {
        }

        try {
            $api = new API();
            if ($api->online) {
                $resp = $api->getRoles($user->ID);
                $roles = (is_array($resp) && is_array($resp['roles'] ?? null)) ? $resp['roles'] : [];
                $best = null;
                $bestLevel = -1;
                foreach ($roles as $role) {
                    if (empty($role['id'])) {
                        continue;
                    }
                    $data = $api->getRole($role['id']);
                    $name = trim((string) ($data['base']['name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }
                    $level = (int) ($data['status']['level'] ?? 0);
                    if ($level > $bestLevel) {
                        $best = $name;
                        $bestLevel = $level;
                    }
                }
                if ($best !== null) {
                    return $best;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('CharacterNameResolver: gagal ambil dari server game.', [
                'user_id' => $user->ID,
                'error' => $e->getMessage(),
            ]);
        }

        return trim($user->truename ?? '') ?: (trim($user->name ?? '') ?: 'User #' . $user->ID);
    }
}
