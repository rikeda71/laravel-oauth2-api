<?php

namespace Tests\Unit\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;

class GoogleOAuthControllerTest extends AbstractAuthControllerTest
{
    const GoogleProvider = 'google';

    public function testShowGoogleOAuthScreen(): void
    {
        // google認証のページにリダイレクトしている
        $resp = $this->post('/auth/google');
        $resp->assertStatus(200);
    }

    public function testCallback(): void
    {
        Socialite::shouldReceive('driver')->with(self::GoogleProvider)->andReturn($this->userProvider);
        $this->requestCallbackEndpoint(self::GoogleProvider)
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
