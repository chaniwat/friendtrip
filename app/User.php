<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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
    protected $hidden = [
        'password'
    ];

    /**
     * Get all events that create by this user
     */
    public function owns() {
        return $this->hasMany('App\Event', 'owner_id', 'id');
    }

    /**
     * Get all event this user participate (joined or leave)
     */
    public function participates() {
        return $this->belongsToMany('App\Event', 'event_join_user', 'user_id', 'event_id')->withPivot('joined_at', 'status', 'staff');
    }

    /**
     * Get all notifications
     */
    public function notifications() {
        return $this->hasMany('App\Notification', 'user_id', 'id');
    }
}
