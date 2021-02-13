<?php

namespace Tests\Unit\UseCases;

use App\Http\Responses\GetUserResponse;
use App\Http\UseCases\GetUserUseCase;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetUserUseCaseTest extends TestCase
{
    private $getUserService;

    private $target;

    protected function setUp(): void
    {
        $this->getUserService = Mockery::mock('App\Http\Services\GetUserService');
        $this->target = new GetUserUseCase($this->getUserService);
    }

    public function testExecute(): void
    {
        // given
        $id = 1;
        $userName = 'dummy';
        $userEmail = 'aaa@test.com';
        $user = new User();
        $user->id = 1;
        $user->name = $userName;
        $user->email = $userEmail;
        $this->getUserService->shouldReceive('execute')
            ->withArgs([$id])
            ->andReturn($user);
        // when
        $actual = $this->target->execute($id);
        // then
        $expected = new GetUserResponse();
        $expected->setUser($user);
        self::assertEquals($expected, $actual);
    }
}
