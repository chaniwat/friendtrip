<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are cannot mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['user_id', 'event_type_id', 'details'];

    /**
     * Get the owner of this event
     */
    public function owner() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * Get participants of this event
     */
    public function participants()
    {
        return $this->belongsToMany('App\User', 'event_user', 'event_id', 'user_id')->withPivot('joined_at');
    }

    /**
     * Get event settings
     */
    public function settings() {
        return $this->belongsToMany('App\EventSetting', 'event_setting_value', 'event_id', 'event_setting_id')->withPivot('value');
    }

    /**
     * Get event type
     */
     public function type()
     {
         return $this->belongsTo('App\EventType', 'event_type_id', 'id');
     }
}
