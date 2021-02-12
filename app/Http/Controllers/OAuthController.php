<?php

namespace App\Http\Controllers;

use App\Http\UseCases\OAuthUseCase;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * @var OAuthUseCase
     */
    private $oauthUseCase;

    public function __construct(OAuthUseCase $oauthUseCase)
    {
        $this->oauthUseCase = $oauthUseCase;
    }

    public function auth(string $provider) {
        // 以下でリダイレクト先URLを取得できるので、これを返却するのもあり
        // Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider) {
        return $this->oauthUseCase->execute($provider);
    }
}
