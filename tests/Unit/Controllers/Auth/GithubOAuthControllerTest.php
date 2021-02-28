<?php

namespace Tests\Unit\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GithubOAuthControllerTest extends AbstractAuthControllerTest
{
    const GithubProvider = 'github';

    public function testShowGoogleOAuthScreen(): void
    {
        // github認証のリダイレクトURLを返す
        $resp = $this->requestAuthEndpoint(self::GithubProvider);
        $resp->assertStatus(200);
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
