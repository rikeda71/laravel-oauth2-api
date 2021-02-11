<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function auth(string $provider) {
        // 以下でリダイレクト先URLを取得できるので、これを返却するのもあり
        // Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider) {
        $user = Socialite::driver($provider)->stateless()->user();
        return $user->getEmail();
    }
}
