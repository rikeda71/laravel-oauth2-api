<?php

namespace App\Http\UseCases;

use App\Http\Services\GetUserService;
use Illuminate\Http\JsonResponse;

class GetUserUseCase
{

    private $getUserService;

    public function __construct(GetUserService $getUserService)
    {
        $this->getUserService = $getUserService;
    }

    public function execute(int $id): JsonResponse
    {
        return new JsonResponse(['name' => $this->getUserService->execute($id)->name]);
    }
}
