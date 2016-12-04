<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function __construct() {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['getEventImage']]);
    }

    /**
     * Get event image
     *
     * @param $filename
     * @return \Illuminate\Http\Response
     */
    public function getEventImage($filename)
    {
        return response()->file(storage_path('app').'/images/event/'.$filename);
    }

    /**
     * @SWG\Post(
     *      path="/images/event",
     *      summary="Upload new event image",
     *      tags={"image"},
     *      description="Upload new image to server",
     *      operationId="newEvent",
     *      consumes={"application/x-www-form-urlencoded"},
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
     *          in="formData",
     *          name="file",
     *          description="Event information",
     *          type="file",
     *          required=true
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Upload successful"
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="No token provided or invalid"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired"
     *      ),
     *      @SWG\Response(
     *          response="422",
     *          description="Invalid file type"
     *      )
     * )
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeEventImage(Request $request)
    {
        $location = Storage::putFileAs('images/event', $request->file('file'),
            Carbon::now()->format('dmYhis')."-".$request->file('file')->hashName()
        );

        return response()->json(["location" => "upload/".$location]);
    }
}
