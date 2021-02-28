<?php

namespace Tests\Unit\Services;

use App\Exceptions\UserLoginException;
use App\Http\Services\UserCreateService;
use App\Http\Services\UserLoginService;
use Auth;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserLoginServiceTest extends TestCase
{
    const ProviderName = 'provider';
    const UserName = 'test';
    const Email = 'aaa@test.com';
    const ProviderUserId = 'provider_user_id';
    const Token = 'dummy';
    const AccessToken = 'accessToken';

    /**
     * @var UserCreateService
     */
    private $userCreateService;

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
        $this->socialiteRepository = Mockery::mock(Socialite::class);
        $this->target = new UserLoginService($this->userCreateService, $this->socialiteRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testExecute(): void
    {
        // given
        // social user
        $mockSocialUser = new \Laravel\Socialite\Two\User();
        $mockSocialUser->id = self::ProviderUserId;
        $mockSocialUser->name = self::UserName;
        $mockSocialUser->email = self::Email;
        $mockSocialUser->token = self::Token;
        $socialiteDriver = Mockery::mock('Laravel\Socialite\Two\AbstractProvider')
            ->shouldReceive('user')
            ->andReturn($mockSocialUser)
            ->getMock();
        $this->socialiteRepository->shouldReceive('driver')
            ->withArgs([self::ProviderName])
            ->andReturn($socialiteDriver);
        // app user
        $appUser = Mockery::mock('App\Models\User');
        $this->userCreateService->shouldReceive('execute')
            ->withArgs([self::ProviderName, self::UserName, self::Email, self::ProviderUserId])
            ->once()
            ->andReturn($appUser);
        Auth::shouldReceive('login')
            ->withArgs([$appUser])
            ->once();

        // when
        $actual = $this->target->execute(self::ProviderName);

        // then
        self::assertEquals($actual, $appUser);
    }

    public function testExecuteWhenLoginFailed(): void
    {
        // throwable
        $this->expectException(UserLoginException::class);
        $this->expectExceptionMessage('unknown exception cause:');

        // given
        $this->socialiteRepository->shouldReceive('driver')
            ->withArgs([self::ProviderName])
            ->times(1)
            ->andThrow(new \Exception('dummy'));

        // when
        $this->target->execute(self::ProviderName);
    }

    public function testExecuteWhenUserCreateFailed(): void
    {
        // throwable
        $this->expectException(UserLoginException::class);

        // given
        // social user
        $mockSocialUser = new \Laravel\Socialite\Two\User();
        $mockSocialUser->id = self::ProviderUserId;
        $mockSocialUser->name = self::UserName;
        $mockSocialUser->email = self::Email;
        $mockSocialUser->token = self::Token;
        $socialiteStateless = Mockery::mock('Laravel\Socialite\Two\AbstractProvider')
            ->shouldReceive('user')
            ->withNoArgs()
            ->andReturn($mockSocialUser)
            ->getMock();
        $socialiteDriver = Mockery::mock('Laravel\Socialite\Contracts\Provider')
            ->shouldReceive('stateless')
            ->withNoArgs()
            ->andReturn($socialiteStateless)
            ->getMock();
        $this->socialiteRepository->shouldReceive('driver')
            ->withArgs([self::ProviderName])
            ->andReturn($socialiteDriver);

        // app user
        $tokenResponse = Mockery::mock('League\OAuth2\Server\Repositories\ClientRepositoryInterface');
        $tokenResponse->accessToken = self::AccessToken;
        $this->userCreateService->shouldReceive('execute')
            ->withArgs([self::ProviderName, self::UserName, self::Email, self::ProviderUserId])
            ->andThrow(new \RuntimeException('dummy'));

        // when
        $this->target->execute(self::ProviderName);
    }
}
