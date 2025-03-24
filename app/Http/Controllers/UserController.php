<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use Str;
use Hash;

class UserController extends Controller
{
    public function registerUser(Request $request): JsonResponse
    {
        try {
            $validate = validator::make($request->all(), [
                'email' => 'required|unique:users|email',
                'password' => 'required|min:8',
                'confirm_password' => 'required|same:password',

            ]);

            $error = collect($validate->errors()->all())->first();
            if ($validate->fails()) {
                return response()->json([
                    'responseCode' => '0',
                    'responseMessage' => 'Validation failed',
                    'errorMessage' => $error,
                ], 422);
            }

            $user = User::create([
                'user_id' => Str::uuid(),
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(
                [
                    'responseCode' => 0,
                    'responseMessage' => 'Registered Sucessfully',
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 1,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }

    public function loginUser(Request $request): JsonResponse
    {
        try {
            $validate = validator::make($request->all(), [
                'email' => 'required|exists:users|email',
                'password' => 'required|min:8',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'responseCode' => 1,
                    'responseMessage' => 'Validation failed',
                    'errorMessage' => $validate->errors()->first(),
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'responseCode' => 1,
                    'errorMessage' => 'The provided credentials are incorrect'
                ], 401);
            }


            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'responseCode' => 0,
                'responseMessage' => 'Logged in successfully',
                'token' => $token,
                'user' => [
                    'user_id' => $user->user_id,
                    'email' => $user->email
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 1,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }

    public function logoutUser(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'responseCode' => 1,
                    'errorMessage' => 'Token missing'
                ], 401);
            }

            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return response()->json([
                    'responseCode' => 1,
                    'errorMessage' => 'Invalid token'
                ], 422);
            }

            $user = $accessToken->tokenable;
            if (!$user) {
                return response()->json([
                    'responseCode' => 1,
                    'errorMessage' => 'User not found'
                ], 401);
            }

            $user->tokens()->delete();

            return response()->json([
                'responseCode' => 0,
                'responseMessage' => 'User logged out successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 1,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:8',
                'password_confirmation' => 'required|same:password'
            ]);

            $error = collect($validator->errors()->all())->first();
            if ($validator->fails()) {
                return response()->json([
                    'responseCode' => '0',
                    'responseMessage' => 'Validation failed',
                    'errorMessage' => $error,
                ], 422);
            }

            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password_confirmation)
            ]);

            return response()->json([
                'responseCode' => '1',
                'responseMessage' => 'Password has been reset successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => '1',
                'errorMessage' => 'A error occurred, please try again',
            ], 500);
        }
    }
}
