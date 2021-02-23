<?php

namespace App\Http\UseCases;

use App\Http\Services\UserLoginService;
use Illuminate\Http\JsonResponse;

class OAuthUseCase
{

    /**
     * @var UserLoginService
     */
    private $userLoginService;

    /**
     * OAuthUseCase constructor.
     * @param UserLoginService $userLoginService
     */
    public function __construct(UserLoginService $userLoginService)
    {
        $this->userLoginService = $userLoginService;
    }

    /**
     * アクセストークンを返す
     * @param string $provider
     * @return string
     * @throws \App\Exceptions\UserLoginException
     * @throws \Throwable
     */
    public function execute(string $provider): string
    {
        return $this->userLoginService->execute($provider);
    }
}
