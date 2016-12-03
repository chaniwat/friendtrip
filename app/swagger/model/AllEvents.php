<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"events", "pagination"}
 * )
 */
class AllEvents
{
    /**
     * @SWG\Property(
     *      type="array",
     *      @SWG\Items(ref="#/definitions/Event")
     * )
     * @var array
     */
    public $events;

    /**
     * @SWG\Property(ref="#/definitions/Pagination")
     * @var Pagination
     */
    public $pagination;
}