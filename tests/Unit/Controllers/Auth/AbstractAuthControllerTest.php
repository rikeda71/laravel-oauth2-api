<?php


namespace Tests\Unit\Controllers\Auth;


use Mockery;
use Tests\TestCase;

abstract class AbstractAuthControllerTest extends TestCase
{

    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected $userProvider;

    public function setUp(): void
    {
        parent::setUp();

        // ログインユーザのmock
        $authUser = Mockery::mock('Laravel\Socialite\Two\User');
        $authUser
            ->shouldReceive('getId')
            ->andReturn(uniqid())
            ->shouldReceive('getEmail')
            ->andReturn(uniqid() . '@test.com')
            ->shouldReceive('getNickName')
            ->andReturn('Nick');

        $this->userProvider = Mockery::mock('Laravel\Socialite\Constracts\Provider');
        $this->userProvider->shouldReceive('user')->andReturn($authUser);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }
}
