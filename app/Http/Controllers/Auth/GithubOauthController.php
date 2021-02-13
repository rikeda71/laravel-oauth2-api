<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserLoginException;
use App\Http\Controllers\Controller;
use App\Http\UseCases\OAuthUseCase;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GithubOauthController extends Controller implements OAuthControllerInterface
{
    /**
     * @var OAuthUseCase
     */
    private $oauthUseCase;

    public function __construct(OAuthUseCase $oauthUseCase)
    {
        $this->oauthUseCase = $oauthUseCase;
    }

    public function auth(): RedirectResponse
    {
        // 以下でリダイレクト先URLを取得できるので、これを返却するのもあり
        // Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return Socialite::driver('github')->redirect();
    }

    public function callback(): JsonResponse
    {
        try {
            return $this->oauthUseCase->execute('github');
        } catch (UserLoginException $ule) {
            return new JsonResponse(['message' => 'login failed with user login exception. cause: ' . $ule->getMessage()]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'login failed with unknown exception. cause: ' . $e->getMessage()]);
        }
    }
}
