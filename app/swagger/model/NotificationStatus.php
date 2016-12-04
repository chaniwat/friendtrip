<?php
/**
 * Created by PhpStorm.
 * User: Meranote
 * Date: 12/4/2016
 * Time: 6:36 AM
 */

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *     type="object",
 *     required={"status"}
 * )
 */
class NotificationStatus
{
    /**
     * @SWG\Property(
     *     default="status"
     * )
     * @var string
     */
    public $status;
}