<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizedRequest;
use App\Http\UseCases\GetUserUseCase;

class UserController extends Controller
{
    /**
     * @var GetUserUseCase
     */
    private $getUserUseCase;

    /**
     * UserController constructor.
     * @param GetUserUseCase $getUserUseCase
     */
    public function __construct(GetUserUseCase $getUserUseCase)
    {
        $this->getUserUseCase = $getUserUseCase;
    }

    public function get(AuthorizedRequest $request)
    {
        $res = $this->getUserUseCase->execute($request->user()->id);
        return $res->toArray();
    }
}
