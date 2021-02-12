<?php

namespace App\Http\Services;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class UserLoginService
{
    private $userCreateService;

    public function __construct(UserCreateService $userCreateService)
    {
        $this->userCreateService = $userCreateService;
    }

    public function execute(string $provider): \Illuminate\Http\JsonResponse
    {
        // Social認証できるか検証
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            if (!$socialUser->token) {
                return response()->json(['message' => 'Failed to login with ' . $provider], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'This user already login. Please try again later' . $provider], 500);
        }

        $appUser = User::where('email', $socialUser->email)->first();
        if (!$appUser) {
            $this->userCreateService->execute($provider, $socialUser->name, $socialUser->email, $socialUser->id);
        }

        $token = $appUser->createToken($socialUser->token)->accessToken;
        return response()->json(['token' => $token]);
    }
}
