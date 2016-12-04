<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct() {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['store', 'show', 'getOwnedEvents', 'getJoinedEvents']]);
    }

    /**
     * @SWG\Post(
     *      path="/users",
     *      summary="Create new user",
     *      tags={"user"},
     *      description="Create new user into database",
     *      operationId="newUser",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="User information",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/NewUser")
     *      ),
     *      @SWG\Response(
     *          response="201",
     *          description="New user has been created"
     *      ),
     *      @SWG\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      )
     * )
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request) {
        $user = new User($request->user);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(null, 201);
    }

    /**
     * @SWG\Get(
     *      path="/users/{user_id}",
     *      summary="Get user information",
     *      tags={"user"},
     *      description="Get user information of {user_id} (Need authentication token for some detail)",
     *      operationId="getUserInfo",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="Token",
     *          type="string",
     *          default="Bearer ",
     *      ),
     *      @SWG\Parameter(
     *          in="path",
     *          name="user_id",
     *          description="User ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Return user information",
     *          @SWG\Schema(ref="#/definitions/User")
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Invalid token"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired"
     *      ),
     *      @SWG\Response(
     *          response="404",
     *          description="User not found"
     *      )
     * )
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function show($id) {
        if(!$user = User::find($id)) {
            return response()->json(["message" => "user_not_found"], 404);
        }

        try {
            if(JWTAuth::parseToken()->authenticate()) {
                return response()->json($user);
            }
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                throw($e);
            } else if($e instanceof TokenInvalidException) {
                throw($e);
            } else if($e instanceof JWTException) {
                return response()->json($user->makeHidden(['email', 'birthdate', 'religion', 'phone']));
            } else {
                throw($e);
            }
        }
    }

    /**
     * @SWG\Put(
     *      path="/users/{user_id}",
     *      summary="Update user information",
     *      tags={"user"},
     *      description="Update user information of {user_id} (Need authentication token for self update or admin token for update to any user)",
     *      operationId="updateUserInfo",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="Token",
     *          type="string",
     *          default="Bearer ",
     *          required=true
     *      ),
     *      @SWG\Parameter(
     *          in="path",
     *          name="user_id",
     *          description="User ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="User information to update",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/UserBody")
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Update user information successful"
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="No token provided or invalid"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired or no permission"
     *      ),
     *      @SWG\Response(
     *          response="404",
     *          description="User not found"
     *      )
     * )
     *
     * @param UpdateUserRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id) {
        if(!$target_user = User::find($id)) {
            return response()->json(["message" => "user_not_found"], 404);
        }

        $current_user = JWTAuth::parseToken()->authenticate();

        if(!$current_user->admin && $current_user->id != $target_user->id) {
            return response()->json(["message" => "no_permission"], 401);
        }

        foreach($request->all() as $key => $value) {
            if(in_array($key, ["id", "password"])) {
                continue;
            }

            $target_user[$key] = $value;
        }
        $target_user->save();

        return response()->json(null, 200);
    }

    /**
     * @SWG\Put(
     *      path="/users/{user_id}/password",
     *      summary="Update user password",
     *      tags={"user"},
     *      description="Update user password of {user_id} (Need authentication token for self update or admin token for update to any user)",
     *      operationId="updateUserPassword",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="Token",
     *          type="string",
     *          default="Bearer ",
     *          required=true
     *      ),
     *      @SWG\Parameter(
     *          in="path",
     *          name="user_id",
     *          description="User ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="New password",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/UpdatePassword")
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Update user password successful"
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="No token provided or invalid"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired or no permission"
     *      ),
     *      @SWG\Response(
     *          response="404",
     *          description="User not found"
     *      )
     * )
     *
     * @param UpdatePasswordRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePasswordRequest $request, $id) {
        if(!$target_user = User::find($id)) {
            return response()->json(["message" => "user_not_found"], 404);
        }

        $current_user = JWTAuth::parseToken()->authenticate();

        if(!$current_user->admin && $current_user->id != $target_user->id) {
            return response()->json(["message" => "no_permission"], 401);
        }

        $target_user->password = Hash::make($request->input("password"));

        $target_user->save();

        return response()->json(null, 200);
    }

    /**
     * @SWG\Get(
     *      path="/users/{user_id}/events/owned",
     *      summary="Get user owned event list",
     *      tags={"user"},
     *      description="Get owned event list of user {user_id} (Need authentication token for some detail)",
     *      operationId="getUserOwnedEventList",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="Token",
     *          type="string",
     *          default="Bearer ",
     *      ),
     *      @SWG\Parameter(
     *          in="path",
     *          name="user_id",
     *          description="User ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="show_per_page",
     *          description="Set show events per page",
     *          type="integer"
     *      ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="page",
     *          description="Pagination page",
     *          type="integer"
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Return owned event list",
     *          @SWG\Schema(ref="#/definitions/AllEvents")
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Invalid token"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired"
     *      ),
     *      @SWG\Response(
     *          response="404",
     *          description="User not found"
     *      )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getOwnedEvents(Request $request, $id) {
        if(!$target_user = User::find($id)) {
            return response()->json(["message" => "user_not_found"], 404);
        }

        $events = $target_user->owned()->paginate($request->input('show_per_page') ? $request->input('show_per_page') : 10);
        $eventsArray = [];

        foreach($events as $event) {
            array_push($eventsArray, EventController::extractEventData($event));
        }

        $pagination = $events->toArray();
        unset($pagination['data']);

        return response()->json(["events" => $eventsArray, "pagination" => $pagination]);
    }

    /**
     * @SWG\Get(
     *      path="/users/{user_id}/events/joined",
     *      summary="Get user joined event list",
     *      tags={"user"},
     *      description="Get joined event list of user {user_id} (Need authentication token for some detail)",
     *      operationId="getUserJoinedEventList",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="Token",
     *          type="string",
     *          default="Bearer ",
     *      ),
     *      @SWG\Parameter(
     *          in="path",
     *          name="user_id",
     *          description="User ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="show_per_page",
     *          description="Set show events per page",
     *          type="integer"
     *      ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="page",
     *          description="Pagination page",
     *          type="integer"
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Return joined event list",
     *          @SWG\Schema(ref="#/definitions/AllEvents")
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Invalid token"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired"
     *      ),
     *      @SWG\Response(
     *          response="404",
     *          description="User not found"
     *      )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getJoinedEvents(Request $request, $id) {
        if(!$target_user = User::find($id)) {
            return response()->json(["message" => "user_not_found"], 404);
        }

        $events = $target_user->joined()->paginate($request->input('show_per_page') ? $request->input('show_per_page') : 10);
        $eventsArray = [];

        foreach($events as $event) {
            array_push($eventsArray, EventController::extractEventData($event));
        }

        $pagination = $events->toArray();
        unset($pagination['data']);

        return response()->json(["events" => $eventsArray, "pagination" => $pagination]);
    }
}
