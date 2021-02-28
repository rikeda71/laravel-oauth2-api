<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        if (Auth::check()) {
            Auth::logout();
            return response()->json(['message' => 'logout success']);
        } else {
            return response()->json(['message' => 'you does not login'], 400);
        }
    }

}
