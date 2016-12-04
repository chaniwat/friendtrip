<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationKey extends Model
{
    public $incrementing = false;
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
    protected $hidden = ["id"];
}
