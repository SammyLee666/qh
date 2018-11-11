<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * register args. phone | password | password_confirmation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = User::validator($request->input());
        if ($validator->fails()) {
            return response()->json([
                "data" => '',
                "errMsg" => $validator->errors()->first(),
            ], 401);
        }

        User::createUser($request->input());
        $credentials = request(['phone', 'password']);
        $token = auth()->guard('api')->attempt($credentials);

        return response()->json([
            "data" => $this->respondWithToken($token),
            "errMsg" => '',
        ], 200);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['phone', 'password']);

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                "data" => '',
                "errMsg" => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            "data" => $this->respondWithToken(auth()->guard('api')->refresh()),
            "errMsg" => '',
        ], 200);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->guard('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->guard('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60
        ];
    }
}
