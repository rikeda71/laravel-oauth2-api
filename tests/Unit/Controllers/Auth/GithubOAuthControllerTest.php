<?php

namespace Tests\Unit\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GithubOAuthControllerTest extends TestCase
{
    const GithubProvider = 'github';

    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $userProvider;

    public function setUp(): void
    {
        parent::setUp();

        // ログインユーザのmock
        $authUser = Mockery::mock('Laravel\Socialite\Two\User');
        $authUser
            ->shouldReceive('getId')
            ->andReturn(uniqid())
            ->shouldReceive('getEmail')
            ->andReturn(uniqid().'@test.com')
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

    public function testShowGoogleOAuthScreen(): void
    {
        // google認証のページにリダイレクトしている
        $resp = $this->requestAuthEndpoint(self::GithubProvider);
        $resp->assertStatus(302);

        $targetUrl = parse_url($resp->headers->get('location'));
        $this->assertEquals('github.com', $targetUrl['host']);
    }

    public function testCallback(): void
    {
        Socialite::shouldReceive('driver')->with(self::GithubProvider)->andReturn($this->userProvider);
        $this->requestCallbackEndpoint(self::GithubProvider)
            ->assertStatus(200);
    }

    /**
     * @param string $provider provider name
     * @return mixed result of showing screen
     */
    private function requestAuthEndpoint(string $provider)
    {
        // OAuthController:auth() にリクエストした結果を返す
        return $this->call('GET', '/auth/' . $provider, ['provider' => $provider]);
    }

    private function requestCallbackEndpoint(string $provider)
    {
        return $this->call('GET', '/auth/' . $provider . '/callback', ['provider' => $provider]);
    }
}
