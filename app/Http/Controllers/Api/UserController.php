<?php

namespace App\Http\Controllers\Api;

use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct() {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['store', 'show']]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\User\StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\User\StoreUserRequest $request) {
        $user = new User($request->user);
        $user->password = Hash::make($request->password);

        $user->save();

        return response()->json(null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::find($id);

        if(!$user) {
            return response()->json(["message" => "user_not_found"], 404);
        }

        return response()->json(compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Requests\User\UpdateUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\User\UpdateUserRequest $request, $id) {
        //
    }

    /**
     * Update the password
     *
     * @param Requests\User\UpdatePasswordRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Requests\User\UpdatePasswordRequest $request, $id) {
        //
    }
}
