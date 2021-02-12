<?php

namespace Tests\Unit\Services;

use App\Http\Services\UserCreateService;
use App\Http\Services\UserLoginService;
use App\Models\User;
use Laravel\Socialite\Contracts\Factory as Socialite;
use PHPUnit\Framework\TestCase;
use Mockery;

class UserLoginServiceTest extends TestCase
{
    const ProviderName = 'provider';
    const UserName = 'test';
    const Email = 'aaa@test.com';
    const UserId = '111';
    const Token = 'dummy';
    const AccessToken = 'accessToken';

    /**
     * @var UserCreateService
     */
    private $userCreateService;

    /**
     * @var User
     */
    private $userRepository;

    /**
     * @var Socialite
     */
    private $socialiteRepository;

    /**
     * @var UserLoginService
     */
    private $target;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userCreateService = Mockery::mock(UserCreateService::class);
        $this->userRepository = Mockery::mock(User::class);
        $this->socialiteRepository = Mockery::mock(Socialite::class);
        $this->target = new UserLoginService($this->userCreateService, $this->userRepository, $this->socialiteRepository);
    }

    public function testExecute(): void
    {
        // given
        // social user
        $mockSocialUser = new \Laravel\Socialite\Two\User();
        $mockSocialUser->token = self::Token;
        $socialiteStateless = Mockery::mock('Laravel\Socialite\Two\AbstractProvider')
            ->shouldReceive('user')
            ->andReturn($mockSocialUser)
            ->getMock();
        $socialiteDriver = Mockery::mock('Laravel\Socialite\Contracts\Provider')
            ->shouldReceive('stateless')
            ->andReturn($socialiteStateless)
            ->getMock();
        $this->socialiteRepository->shouldReceive('driver')
            ->withArgs([self::ProviderName])
            ->andReturn($socialiteDriver);
        // app user
        $tokenResponse = Mockery::mock('League\OAuth2\Server\Repositories\ClientRepositoryInterface');
        $tokenResponse->accessToken = self::AccessToken;
        $firstUser = Mockery::mock('App\Models\User')
            ->shouldReceive('createToken')
            ->withArgs([$mockSocialUser->token])
            ->andReturn($tokenResponse)
            ->getMock();
        $appUser = Mockery::mock('App\Models\User')
            ->shouldReceive('first')
            ->andReturn($firstUser)
            ->getMock();
        $this->userRepository->shouldReceive('where')
            ->withArgs(['email', $mockSocialUser->email])
            ->andReturn($appUser);
        $this->userCreateService->shouldReceive('execute')
            ->withArgs([self::ProviderName, self::UserName, self::Email, self::UserId])
            ->andReturn(null);
        // when
        $accessToken = $this->target->execute(self::ProviderName);
        // then
        self::assertEquals(self::AccessToken, $accessToken);
    }
}
