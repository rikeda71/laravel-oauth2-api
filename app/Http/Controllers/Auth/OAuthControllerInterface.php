<?php

namespace App\Http\Controllers\Auth;

interface OAuthControllerInterface {
    public function auth();
    public function callback();
}
