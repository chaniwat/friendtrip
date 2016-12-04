<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"notifications", "pagination"}
 * )
 */
class AllNotification
{
    /**
     * @SWG\Property(
     *      type="array",
     *      @SWG\Items(ref="#/definitions/Notification")
     * )
     * @var array
     */
    public $notifications;

    /**
     * @SWG\Property(ref="#/definitions/Pagination")
     * @var Pagination
     */
    public $pagination;
}