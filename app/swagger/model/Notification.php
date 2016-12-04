<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *     type="object",
 *      allOf={
 *          @SWG\Items(ref="#/definitions/NotificationStatus")
 *      },
 *     required={"id", "key_id", "value", "status"}
 * )
 */
class Notification
{
    /**
     * @SWG\Property
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(
     *     default="Notification_key"
     * )
     * @var string
     */
    public $key_id;

    /**
     * @SWG\Property(
     *     default="JSON Object as string"
     * )
     * @var string
     */
    public $value;
}