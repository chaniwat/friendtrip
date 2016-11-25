<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends Controller
{

    public function __construct() {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    /**
     * Return the JWT
     *
     * @param Requests\Auth\TokenRequest $request
     * @return mixed
     */
    public function authenticate(Requests\Auth\TokenRequest $request) {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT with authenticate user
        $user = JWTAuth::toUser($token);

        if($request->user) {
            return response()->json(["token" => $token, "user" => $user]);
        } else {
            return response()->json(["token" => $token]);
        }
    }

    /**
     * Return current logged in user
     *
     * @return mixed
     */
    public function index() {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['error' => 'user_not_found'], 404);
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

}
