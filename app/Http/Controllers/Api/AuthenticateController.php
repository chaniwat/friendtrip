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
     *          description="User identity",
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
     *          @SWG\Schema(ref="#/definitions/MessageResponse")
     *      )
     * )
     *
     * @param Requests\Auth\TokenRequest $request
     * @return mixed
     */
    public function authenticate(Requests\Auth\TokenRequest $request) {
        $credentials = $request->only('email', 'password');

        // verify the credentials and create a token for the user
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'invalid_credentials'], 401);
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
     *      description="Get user information of given token",
     *      operationId="getAuthUserInfo",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="Token",
     *          type="string",
     *          default="Bearer ",
     *          required=true
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="User information",
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="No token provided or invalid"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired"
     *      )
     * )
     *
     * @return mixed
     */
    public function index() {
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json($user);
    }

}
