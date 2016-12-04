<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
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
    protected $hidden = [
        'owner_id'
    ];

    /**
     * Get the owner of this event
     */
    public function owner() {
        return $this->belongsTo('App\User', 'owner_id', 'id');
    }

    /**
     * Get participants of this event
     */
    public function participants()
    {
        return $this->belongsToMany('App\User', 'event_join_user', 'event_id', 'user_id')->withPivot('joined_at', 'status', 'staff');
    }

    /**
     * Get event settings
     */
    public function settings() {
        return $this->belongsToMany('App\EventSetting', 'event_setting_value', 'event_id', 'event_setting_id')->withPivot('value');
    }
}
