<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\EventSetting;
use App\EventType;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventController extends Controller
{
    public function __construct() {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['index', 'show', 'getTypes']]);
    }

    /**
     * Extract event data for sending in api
     *
     * @param Event $event
     * @return array
     * @throws \Exception
     */
    protected function extractEventData(Event $event) {
        $eventArray = $event->toArray();

        // Extract event settings
        $eventArray['settings'] = array();

        foreach($event->settings->all() as $setting) {
            array_push($eventArray['settings'], ["event_setting_id" => $setting->id, "value" => $setting->pivot->value]);
        }

        $owner = $event->owner;

        // Check if user has authenticated (show strict user info)
        try {
            if($user = JWTAuth::parseToken()->authenticate()) {
                $eventArray['owner'] = $owner->toArray();
            }

            // Check join status of current authenticated user
            if($user) {
                if($owner->id == $user->id) {
                    $eventArray['join_status'] = 'owner';
                } else {
                    $eventArray['join_status'] = !!$event->participants()->find($user->id);
                }
            }
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                throw($e);
            } else if($e instanceof TokenInvalidException) {
                throw($e);
            } else if($e instanceof JWTException) {
                $eventArray['owner'] = $owner->makeHidden(['email', 'birthdate', 'religion', 'phone'])->toArray();
            } else {
                throw($e);
            }
        }

        // Get participant count
        $eventArray['participant_count'] = $event->participants()->where('status', 'JOIN')->count();

        return $eventArray;
    }

    /**
     * @SWG\Get(
     *      path="/events",
     *      summary="Get all event information",
     *      tags={"event"},
     *      description="Get all event information",
     *      operationId="getAllEvents",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="Token",
     *          type="string",
     *          default="Bearer ",
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
     *      @SWG\Parameter(
     *          in="query",
     *          name="full_event",
     *          description="Show full event or not?",
     *          type="boolean"
     *      ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="search",
     *          description="Search event by name",
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="place",
     *          description="Search event by place",
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="type",
     *          description="Filter event by type",
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="All Events information",
     *          @SWG\Schema(ref="#/definitions/AllEvents")
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Invalid token"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired"
     *      )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $events = Event::paginate($request->input('show_per_page') ? $request->input('show_per_page') : 10);
        $eventsArray = [];

        foreach($events as $event) {
            array_push($eventsArray, $this->extractEventData($event));
        }

        $pagination = $events->toArray();
        unset($pagination['data']);

        return response()->json(["events" => $eventsArray, "pagination" => $pagination]);
    }

    /**
     * @SWG\Post(
     *      path="/events",
     *      summary="Create new event",
     *      tags={"event"},
     *      description="Create new event into database (owner is current authentication user (token))",
     *      operationId="newEvent",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          in="header",
     *          name="Authorization",
     *          description="Token",
     *          type="string",
     *          default="Bearer ",
     *      ),
     *      @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="Event information",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/EventBody")
     *      ),
     *      @SWG\Response(
     *          response="201",
     *          description="New event has been created"
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
     *          description="Invalid parameters"
     *      )
     * )
     *
     * @param  Requests\Event\StoreEventRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\Event\StoreEventRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $event = new Event($request->except("settings"));
        $event->owner()->associate($user);
        $event->save();

        if($request->input("settings")) {
            foreach($request->input("settings") as $setting) {
                $event->settings()->attach(EventSetting::find($setting["event_setting_id"]), ["value" => $setting["value"]]);
            }
        }

        return response()->json(null, 201);
    }

    /**
     * @SWG\Get(
     *      path="/events/{event_id}",
     *      summary="Get event information",
     *      tags={"event"},
     *      description="Get information of event {event_id}",
     *      operationId="getEventInfo",
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
     *          name="event_id",
     *          description="Event ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Event information",
     *          @SWG\Schema(ref="#/definitions/Event")
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
     *          description="Event not found"
     *      )
     * )
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::find($id);

        if($event == null) {
            return response()->json(['message' => 'event_not_found'], 404);
        }

        $eventArray = $this->extractEventData($event);

        return response()->json($eventArray);
    }

    /**
     * @SWG\Put(
     *      path="/events/{event_id}",
     *      summary="Update event information",
     *      tags={"event"},
     *      description="Update event information of {event_id} (Need authentication token for self update or admin token for update to any event)",
     *      operationId="updateEvent",
     *      consumes={"application/json"},
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
     *          name="event_id",
     *          description="Event ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          description="Event information",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/EventBody")
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Update event information successful"
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
     *          response="422",
     *          description="Invalid parameters"
     *      )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response()->json(null);
    }

    /**
     * @SWG\Post(
     *      path="/events/{event_id}/join",
     *      summary="Join event",
     *      tags={"event"},
     *      description="Join to event",
     *      operationId="joinEvent",
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
     *          name="event_id",
     *          description="Event ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Join event success"
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
     *          response="404",
     *          description="No event found"
     *      )
     * )
     *
     * @param Request $request
     * @param $id
     */
    public function joinEvent(Request $request, $id) {

    }

    /**
     * @SWG\Post(
     *      path="/events/{event_id}/leave",
     *      summary="Leave event",
     *      tags={"event"},
     *      description="Leave to event",
     *      operationId="leaveEvent",
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
     *          name="event_id",
     *          description="Event ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="leave event success"
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
     *          response="404",
     *          description="No event found or not joined to event"
     *      )
     * )
     *
     * @param Request $request
     * @param $id
     */
    public function leaveEvent(Request $request, $id) {

    }

    /**
     * @SWG\Post(
     *      path="/events/{event_id}/cancel",
     *      summary="Cancel event",
     *      tags={"event"},
     *      description="Cancel the event",
     *      operationId="cancelEvent",
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
     *          name="event_id",
     *          description="Event ID",
     *          type="integer",
     *          required=true
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="cancel event success"
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="No token provided or invalid"
     *      ),
     *      @SWG\Response(
     *          response="401",
     *          description="Token expired or no permission (not an owner of event)"
     *      ),
     *      @SWG\Response(
     *          response="404",
     *          description="No event found"
     *      )
     * )
     *
     * @param Request $request
     * @param $id
     */
    public function cancelEvent(Request $request, $id) {

    }
}
