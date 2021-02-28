<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

interface OAuthControllerInterface {
    public function getRedirectUrl();
    public function callback();
}
