<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
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
    protected $hidden = ["user_id"];

    public function user() {
        return $this->belongsTo('App\User', "user_id", "id");
    }

    public function key() {
        return $this->belongsTo('App\NotificationKey', "key_id", "id");
    }

}
