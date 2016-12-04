<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *     type="object",
 *     allOf={
 *          @SWG\Items(ref="#/definitions/User")
 *     },
 * )
 */
class EventParticipant
{
    /**
     * @SWG\Property(
     *     type="string",
     *     default="yyyy-MM-dd hh:mm:ss"
     * )
     * @var \DateTime
     */
    public $joined_at;

    /**
     * @SWG\Property(
     *     enum={"JOIN", "LEAVE", "KICK"}
     * )
     * @var string
     */
    public $status;

    /**
     * @SWG\Property(
     *     default=false
     * )
     * @var boolean
     */
    public $staff;
}