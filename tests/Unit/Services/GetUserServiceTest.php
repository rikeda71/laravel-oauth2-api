<?php

namespace Tests\Unit\Services;

use App\Http\Services\GetUserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Mockery;

class GetUserServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $userRepository;

    /**
     * @var GetUserService
     */
    private $target;

    protected function setUp(): void
    {
        $this->userRepository = Mockery::mock('App\Models\User');
        $this->target = new GetUserService($this->userRepository);
    }

    public function testExecute(): void
    {
        // given
        $id = 1;
        $users = Mockery::mock('App\Models\User')
            ->shouldReceive('first')
            ->andReturn(new User())
            ->getMock();
        $this->userRepository->shouldReceive('where')
            ->withArgs(['id', $id])
            ->andReturn($users);
        // when
        $this->target->execute($id);
        // then
        self::assertTrue(true);

    }

}
