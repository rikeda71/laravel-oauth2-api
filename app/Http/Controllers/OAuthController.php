<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function auth(string $provider) {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider) {
        $user = Socialite::driver($provider)->user();
        return $user->getEmail();
    }
}
