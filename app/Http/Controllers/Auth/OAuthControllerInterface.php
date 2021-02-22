<?php

namespace App\Http\Controllers\Auth;

interface OAuthControllerInterface {
    public function getRedirectUrl();
    public function callback();
}
