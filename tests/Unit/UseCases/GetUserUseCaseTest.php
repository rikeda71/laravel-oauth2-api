<?php

namespace Tests\Unit\UseCases;

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
        $user = new User();
        $user->name = $userName;
        $this->getUserService->shouldReceive('execute')
            ->withArgs([$id])
            ->andReturn($user);
        // when
        $actual = $this->target->execute($id);
        // then
        self::assertEquals(new JsonResponse(['name' => $userName]), $actual);
    }
}
