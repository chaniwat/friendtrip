<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\EventSetting;
use App\EventType;

use App\User;
use Carbon\Carbon;
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
        $this->middleware('jwt.auth', ['except' => ['index', 'show', 'getTypes', 'getParticipants']]);
    }

    /**
     * Extract event data for sending in api
     *
     * @param Event $event
     * @return array
     * @throws \Exception
     */
    public static function extractEventData(Event $event) {
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
     * Extract event data for sending in api
     *
     * @param User $participant
     * @return array
     * @throws \Exception
     */
    public static function extractParticipantData(User $participant) {
        $participantArray = [];

        // Check if user has authenticated (show strict user info)
        try {
            if($user = JWTAuth::parseToken()->authenticate()) {
                $participantArray = $participant->toArray();
                unset($participantArray['pivot']);
            }
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                throw($e);
            } else if($e instanceof TokenInvalidException) {
                throw($e);
            } else if($e instanceof JWTException) {
                $participantArray = $participant->makeHidden(['email', 'birthdate', 'religion', 'phone'])->toArray();
                unset($participantArray['pivot']);
            } else {
                throw($e);
            }
        }

        // Extract participant status
        $participantArray['status'] = $participant->pivot->status;

        if($participantArray['status'] == "JOIN") {
            $participantArray['joined_at'] = $participant->pivot->joined_at;
            $participantArray['staff'] = $participant->pivot->staff;
        }

        return $participantArray;
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
        // TODO finish all query parameters in get all events (full_event(filter), type(filter), search, place)

        $events = Event::paginate($request->input('show_per_page') ? $request->input('show_per_page') : 10);
        $eventsArray = [];

        foreach($events as $event) {
            array_push($eventsArray, EventController::extractEventData($event));
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

        $eventArray = EventController::extractEventData($event);

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
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!$target_event = Event::find($id)) {
            return response()->json(["message" => "event_not_found"], 404);
        }

        $current_user = JWTAuth::parseToken()->authenticate();

        if(!$current_user->admin && $current_user->id != $target_event->owner->id) {
            return response()->json(["message" => "no_permission"], 401);
        }

        foreach($request->all() as $key => $value) {
            if(in_array($key, ["id", "owner_id", "destination_place", "destination_place_id", "destination_latitude", "destination_longitude", "status", "created_at", "updated_at"])) {
                continue;
            }

            $target_event[$key] = $value;
        }

        $target_event->save();

        return response()->json(null, 200);
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
     *          description="No token provided or invalid or can't join event"
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function joinEvent($id)
    {
        if(!$target_event = Event::find($id)) {
            return response()->json(["message" => "event_not_found"], 404);
        }

        // Check is event already cancel
        if($target_event->status == "CANCEL") {
            return response()->json(["message" => "event_already_cancel"], 400);
        }

        // Check event time
        $startTime = Carbon::parse($target_event->start_date);
        $endTime = Carbon::parse($target_event->end_date);

        if($endTime->isPast()) {
            return response()->json(["message" => "event_already_finished"], 400);
        } else if($startTime->isPast()) {
            return response()->json(["message" => "event_already_started"], 400);
        }

        // Check joined own event
        $current_user = JWTAuth::parseToken()->authenticate();
        if(!$current_user->admin && $current_user->id == $target_event->owner->id) {
            return response()->json(["message" => "cannot_join_owned_event"], 400);
        }

        // Check already joined?
        $participate = $target_event->participants()->where('id', $current_user->id)->first();
        if($participate && $participate->pivot->status == "JOIN") {
            return response()->json(["message" => "already_joined"], 400);
        }

        // Check full participant
        $maxParticipant = $target_event->settings->find("MAX_PARTICIPANT");
        if($maxParticipant && $maxParticipant->pivot->value != 0 && $target_event->participants()->where('status', 'JOIN')->count() == $maxParticipant->pivot->value) {
            return response()->json(["message" => "event_full"], 400);
        }

        // TODO Check another setting with current user (ALLOW_AGE, ALLOW_GENDER, ALLOW_RELIGION)

        // Join event
        if($participate) {
            // update pivot if has been joined in past
            $target_event->participants()->updateExistingPivot($current_user->id, ['status' => 'JOIN']);
        } else {
            // attach if not join by first
            $target_event->participants()->attach($current_user);
        }

        return response()->json(null, 200);
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
     *          description="No token provided or invalid or can't leave event"
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
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function leaveEvent($id)
    {
        if(!$target_event = Event::find($id)) {
            return response()->json(["message" => "event_not_found"], 404);
        }

        // Check is event already cancel
        if($target_event->status == "CANCEL") {
            return response()->json(["message" => "event_already_cancel"], 400);
        }

        // Check is joined event?
        $current_user = JWTAuth::parseToken()->authenticate();

        $participate = $target_event->participants()->where('id', $current_user->id)->first();
        if($participate && $participate->pivot->status != "JOIN") {
            return response()->json(["message" => "not_joined"], 400);
        }

        // Check event time
        $startTime = Carbon::parse($target_event->start_date);
        $endTime = Carbon::parse($target_event->end_date);

        if($endTime->isPast()) {
            return response()->json(["message" => "event_already_finished"], 400);
        } else if($startTime->isPast()) {
            return response()->json(["message" => "event_already_started"], 400);
        }

        // leave event (save pivot status)
        $target_event->participants()->updateExistingPivot($current_user->id, ['status' => 'LEAVE']);

        return response()->json(null, 200);
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
     *          description="No token provided or invalid or can't cancel event"
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelEvent($id)
    {
        if(!$target_event = Event::find($id)) {
            return response()->json(["message" => "event_not_found"], 404);
        }

        // Check is owner of event
        $current_user = JWTAuth::parseToken()->authenticate();
        if(!$current_user->admin && $current_user->id != $target_event->owner->id) {
            return response()->json(["message" => "no_permission"], 401);
        }

        // Check is event already cancel
        if($target_event->status == "CANCEL") {
            return response()->json(["message" => "event_already_cancel"], 400);
        }

        // Check event time
        $startTime = Carbon::parse($target_event->start_date);
        $endTime = Carbon::parse($target_event->end_date);

        if($endTime->isPast()) {
            return response()->json(["message" => "event_already_finished"], 400);
        } else if($startTime->isPast()) {
            return response()->json(["message" => "event_already_started"], 400);
        }

        // cancel event
        $target_event->status = "CANCEL";
        $target_event->save();

        return response()->json(null, 200);
    }

    /**
     * @SWG\Get(
     *      path="/events/{event_id}/participants",
     *      summary="Get participant list",
     *      tags={"event"},
     *      description="Get participant list of event {event_id} (Need authentication token for some detail)",
     *      operationId="getEventParticipantList",
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
     *          description="Return participant list",
     *          @SWG\Schema(ref="#/definitions/EventParticipants")
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
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getParticipants(Request $request, $id) {
        if(!$target_event = Event::find($id)) {
            return response()->json(["message" => "event_not_found"], 404);
        }

        $participants = $target_event->participants()->paginate($request->input('show_per_page') ? $request->input('show_per_page') : 10);
        $participantsArray = [];

        foreach($participants as $participant) {
            array_push($participantsArray, EventController::extractParticipantData($participant));
        }

        $pagination = $participants->toArray();
        unset($pagination['data']);

        return response()->json(["participants" => $participantsArray, "pagination" => $pagination]);
    }

}
