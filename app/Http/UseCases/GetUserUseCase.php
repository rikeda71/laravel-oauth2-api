<?php

namespace App\Http\UseCases;

use App\Http\Responses\GetUserResponse;
use App\Http\Services\GetUserService;

class GetUserUseCase
{

    /**
     * @var GetUserService
     */
    private $getUserService;

    /**
     * GetUserUseCase constructor.
     * @param GetUserService $getUserService
     */
    public function __construct(GetUserService $getUserService)
    {
        $this->getUserService = $getUserService;
    }

    /**
     * @param int $id
     * @return GetUserResponse
     */
    public function execute(int $id): GetUserResponse
    {
        $user = $this->getUserService->execute($id);
        $res = new GetUserResponse();
        $res->setUser($user);
        return $res;
    }
}
