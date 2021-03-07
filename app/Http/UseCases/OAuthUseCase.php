<?php

namespace App\Http\UseCases;

use App\Http\Services\UserLoginService;

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
     * @param string $provider
     * @return \App\Models\User
     * @throws \App\Exceptions\UserLoginException
     * @throws \Throwable
     */
    public function execute(string $provider): \App\Models\User
    {
        return $this->userLoginService->execute($provider);
    }
}
