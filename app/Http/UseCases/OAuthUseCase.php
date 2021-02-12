<?php

namespace App\Http\UseCases;

use App\Http\Services\UserCreateService;
use App\Http\Services\UserLoginService;

class OAuthUseCase
{

    private $userLoginService;

    public function __construct(UserLoginService $userLoginService, UserCreateService $userCreateService)
    {
        $this->userLoginService = $userLoginService;
    }

    public function execute(string $provider): \Illuminate\Http\JsonResponse
    {
        return $this->userLoginService->execute($provider);
    }
}
