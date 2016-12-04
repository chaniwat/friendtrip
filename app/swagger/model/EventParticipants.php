<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *     type="object"
 * )
 */
class EventParticipants
{
    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref="#/definitions/EventParticipant")
     * )
     * @var array
     */
    public $participants;

    /**
     * @SWG\Property(ref="#/definitions/Pagination")
     * @var Pagination
     */
    public $pagination;
}