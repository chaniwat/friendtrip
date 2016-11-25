<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\User;
use App\EventType;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            // ignored
        }

        $events = Event::paginate(10);
        $eventsArray = [];

        foreach($events as $event) {
            array_push($eventsArray, $this->extractEventData($event, $user));
        }

        $pagination = $events->toArray();
        unset($pagination['data']);

        return response()->json(["events" => $eventsArray, "pagination" => $pagination]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\Event\StoreEventRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\Event\StoreEventRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $event = new Event(collect($request->event)->except(['type'])->all());
        $event->owner()->associate($user);
        $event->type()->associate(EventType::find($request->input('event.type')));
        $event->save();

        return response()->json(null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            // ignored
        }

        $event = Event::find($id);

        if($event == null) {
            return response()->json(['error' => 'event_not_found'], 404);
        }

        $eventArray = $this->extractEventData($event, $user);
        $eventArray['details'] = $event->details;

        return response()->json($eventArray);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Get all event types
     *
     * @return \Illuminate\Http\Response
     */
    public function getTypes()
    {
        $types = EventType::all();

        return response()->json(compact('types'));
    }

    /**
     * Join event
     *
     * @param Request $request
     * @param $id
     */
    public function joinEvent(Request $request, $id) {

    }

    /**
     * Leave event
     *
     * @param Request $request
     * @param $id
     */
    public function leaveEvent(Request $request, $id) {

    }

    /**
     * Cancel event
     *
     * @param Request $request
     * @param $id
     */
    public function cancelEvent(Request $request, $id) {

    }

    /**
     * Extract event data for sending over api
     */
    public function extractEventData(Event $event, $user) {
        $eventArray = $event->toArray();

        $owner = $event->owner;
        $eventArray['owner'] = [
            "id" => $owner->id,
            "name" => $owner->name
        ];

        $type = $event->type;
        $eventArray['type'] = [
            "id" => $type->id,
            "name" => $type->name
        ];

        if($user) {
            if($owner->id == $user->id) {
                $eventArray['join'] = 'owner';
            } else {
                $eventArray['join'] = !!$event->participants()->find($user->id);
            }
        }

        return $eventArray;
    }
}
