<?php


namespace Tests\Unit\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLogout(): void
    {
        $user = User::factory()->create();
        \Auth::login($user);
        $resp = $this->call('GET', '/auth/logout', []);
        self::assertEquals(200, $resp->status());
        self::assertGuest();
    }

}
