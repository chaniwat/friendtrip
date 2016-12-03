<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      allOf={
 *          @SWG\Items(ref="#/definitions/EventBody")
 *      },
 *      required={"id"}
 * )
 */
class Event
{
    /**
     * @SWG\Property
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(ref="#/definitions/User")
     * @var User
     */
    public $owner;

    /**
     * @SWG\Property
     * @var integer
     */
    public $participant_count;

    /**
     * @SWG\Property(
     *     default="owner or boolean (true = join, false = not join yet) | if sent token for checking status"
     * )
     * @var string
     */
    public $join_status;
}