<?php

namespace App\Http\Services;

use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use http\Exception\RuntimeException;
use Laravel\Socialite\Contracts\Factory as Socialite;

class UserLoginService
{
    /**
     * @var UserCreateService
     */
    private $userCreateService;

    /**
     * @var User
     */
    private $userRepository;

    /**
     * @var Socialite
     */
    private $socialiteRepository;

    public function __construct(UserCreateService $userCreateService, User $userRepository, Socialite $socialiteRepository)
    {
        $this->userCreateService = $userCreateService;
        $this->userRepository = $userRepository;
        $this->socialiteRepository = $socialiteRepository;
    }

    public function execute(string $provider): string
    {
        // Social認証できるか検証
        try {
            $socialUser = $this->socialiteRepository->driver($provider)->stateless()->user();
            if (!$socialUser->token) {
                 throw new RuntimeException('Failed to login with ' . $provider);
            }
        } catch (ClientException $ce) {
            return response()->json(['message' => 'throw client exception [' . $ce . ']']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'unknown exception cause:' . $e], 500);
        }

        $appUser = $this->userRepository->where('email', $socialUser->email)->first();
        if (!$appUser) {
            try {
                $this->userCreateService->execute($provider, $socialUser->name, $socialUser->email, $socialUser->id);
            } catch (\RuntimeException $re) {
                return response()->json(['message' => $re]);
            }
        }

        return $appUser->createToken($socialUser->token)->accessToken;
    }
}
