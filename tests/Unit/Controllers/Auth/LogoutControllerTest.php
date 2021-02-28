<?php


namespace Tests\Unit\Controllers\Auth;

use Mockery;
use Tests\TestCase;

class LogoutControllerTest extends AbstractAuthControllerTest
{

    public function testLogout(): void
    {
        $resp = $this->call('GET', '/logout', []);
        self::assertEquals(200, $resp->status());
    }

}
