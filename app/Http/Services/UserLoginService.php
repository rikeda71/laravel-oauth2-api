<?php

namespace App\Http\Services;

use App\Exceptions\UserLoginException;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Contracts\Factory as Socialite;

class UserLoginService
{
    /**
     * @var UserCreateService
     */
    private $userCreateService;

    /**
     * @var Socialite
     */
    private $socialiteRepository;

    /**
     * UserLoginService constructor.
     * @param UserCreateService $userCreateService
     * @param Socialite $socialiteRepository
     */
    public function __construct(UserCreateService $userCreateService, Socialite $socialiteRepository)
    {
        $this->userCreateService = $userCreateService;
        $this->socialiteRepository = $socialiteRepository;
    }

    /**
     * @param string $provider
     * @return string
     * @throws UserLoginException
     * @throws \Throwable
     */
    public function execute(string $provider): string
    {
        // Social認証できるか検証
        try {
            $socialUser = $this->socialiteRepository->driver($provider)->stateless()->user();
            if (!$socialUser->token) {
                throw new UserLoginException('failed to login with ' . $provider);
            }
        } catch (ClientException $ce) {
            throw new UserLoginException('client exception cause:' . $ce);
        } catch (\Exception $e) {
            throw new UserLoginException('unknown exception cause:' . $e);
        }

        try {
            $appUser = $this->userCreateService->execute($provider, $socialUser->name, $socialUser->email, $socialUser->id);
        } catch (\RuntimeException $re) {
            throw new UserLoginException($re);
        }

        return $appUser->createToken($socialUser->token)->accessToken;
    }
}
