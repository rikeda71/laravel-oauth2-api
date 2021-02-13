<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserLoginException;
use App\Http\Controllers\Controller;
use App\Http\UseCases\OAuthUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/**
 * OAuthUseCase を呼び出して、ソーシャルログインを行うコントローラのサンプル実装
 * Class OAuthController
 * @package App\Http\Controllers\Auth
 */
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
        try {
            return $this->oauthUseCase->execute($provider);
        } catch (UserLoginException $ule) {
            return new JsonResponse(['message' => 'login failed with user login exception. cause: ' . $ule->getMessage()]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'login failed with unknown exception. cause: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        if (Auth::check())
        {
            Auth::user()->token()->revoke();
            return response()->json(['message' => 'logout success']);
        } else {
            return response()->json(['message' => 'you does not login'], 400);
        }
    }
}
