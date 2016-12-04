<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *     type="object",
 *     required={"user_id"}
 * )
 */
class EventInviteUser
{
    /**
     * @SWG\Property
     * @var integer
     */
    public $user_id;
}