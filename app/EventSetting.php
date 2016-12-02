<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventSetting extends Model
{
    // Constant for keys
    const ALLOW_RELIGION = 'ALLOW_RELIGION';
    const ALLOW_AGE = 'ALLOW_AGE';
    const ALLOW_GENDER = 'ALLOW_GENDER';
    const MAX_PARTICIPANT = 'MAX_PARTICIPANT';

    // Model settings
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
