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
     * @SWG\Post(
     *      path="/authentication",
     *      summary="Authentication (Request token)",
     *      tags={"authentication"},
     *      description="Request token for user auth needed APIs",
     *      operationId="authentication",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="User identity information",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/AuthenticationInfo")
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Successful request token",
     *          @SWG\Schema(ref="#/definitions/Token")
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Invalid credential",
     *          @SWG\Schema(ref="#/definitions/Error")
     *      ),
     *      @SWG\Response(
     *          response="500",
     *          description="Internal server error",
     *          @SWG\Schema(ref="#/definitions/Error")
     *      )
     * )
     *
     * @param Requests\Auth\TokenRequest $request
     * @return mixed
     */
    public function authenticate(Requests\Auth\TokenRequest $request) {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['message' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT with authenticate user
        $user = JWTAuth::toUser($token);

        if($request->get_info) {
            return response()->json(["token" => $token, "user" => $user]);
        } else {
            return response()->json(["token" => $token]);
        }
    }

    /**
     * @SWG\Get(
     *      path="/authentication",
     *      summary="Get authenticate user information",
     *      tags={"authentication"},
     *      description="Get information of given token user information",
     *      operationId="getAuthUserInfo",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="JWT-Token",
     *          type="string",
     *          default="Bearer ",
     *          required=true
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Return user information",
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Token not provided or invalid",
     *          @SWG\Schema(ref="#/definitions/Error")
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired",
     *          @SWG\Schema(ref="#/definitions/Error")
     *      ),
     *      @SWG\Response(
     *          response="404",
     *          description="User not found",
     *          @SWG\Schema(ref="#/definitions/Error")
     *      ),
     *      @SWG\Response(
     *          response="500",
     *          description="Internal server error",
     *          @SWG\Schema(ref="#/definitions/Error")
     *      )
     * )
     *
     * @return mixed
     */
    public function index() {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['message' => 'user_not_found'], 404);
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

}
