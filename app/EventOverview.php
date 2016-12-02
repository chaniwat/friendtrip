<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EventOverview <br>
 * Views model only (DON'T USE TO UPDATE)
 * @package App
 */
class EventOverview extends Model
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
    protected $hidden = [];
}
