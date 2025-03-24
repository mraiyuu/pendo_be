<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function registerUser(Request $request): JsonResponse
    {
        return response()->json(['hit']);
    }

    public function loginUser(Request $request): JsonResponse
    {
        return response()->json(['hit']);
    }

    public function logoutUser(Request $request): JsonResponse
    {
        return response()->json(['hit']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        return response()->json(['hit']);
    }



    

}
