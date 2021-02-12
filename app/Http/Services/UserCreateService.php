<?php

namespace App\Http\Services;

use App\Models\SocialRelation;
use App\Models\User;

class UserCreateService
{
    public function __construct()
    {
        //
    }

    public function execute(string $provider, string $name, string $email, string $socialUserId): void
    {
        try {
            // https://github.com/HeshamAdel007/MoviesApp/blob/master/back-end/app/Http/Controllers/Api/Auth/SocialiteLoginController.php
            // TODO: パスワードも用意できるようにしてみる
            $appUser = User::create([
                'name' => $name,
                'email' => $email,
            ]);
            // ソーシャルログイン用のテーブルのマイグレーションを作ってみる
            $socialRelation = $appUser->socialLogin()->where('provider', $provider)->first();
            if (!$socialRelation) {
                SocialRelation::create([
                    'provider' => $provider,
                    'user_id' => $appUser->id,
                    'provider_user_id' => $socialUserId,
                ]);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('failed to create application user. cause: ' . $e);
        }
    }
}
