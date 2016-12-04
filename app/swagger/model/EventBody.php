<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"name", "destination_place", "start_date", "end_date", "appointment_place", "appointment_time", "details", "type", "approximate_cost", "status"}
 * )
 */
class EventBody
{
    /**
     * @SWG\Property
     * @var string
     */
    public $name;

    /**
     * @SWG\Property
     * @var string
     */
    public $destination_place;

    /**
     * @SWG\Property
     * @var integer
     */
    public $destination_place_id;

    /**
     * @SWG\Property
     * @var double
     */
    public $destination_latitude;

    /**
     * @SWG\Property
     * @var double
     */
    public $destination_longitude;

    /**
     * @SWG\Property(
     *     type="string",
     *     default="yyyy-MM-dd hh:mm:ss"
     * )
     * @var \DateTime
     */
    public $start_date;

    /**
     * @SWG\Property(
     *     type="string",
     *     default="yyyy-MM-dd hh:mm:ss"
     * )
     * @var \DateTime
     */
    public $end_date;

    /**
     * @SWG\Property
     * @var string
     */
    public $appointment_place;

    /**
     * @SWG\Property
     * @var integer
     */
    public $appointment_place_id;

    /**
     * @SWG\Property
     * @var double
     */
    public $appointment_latitude;

    /**
     * @SWG\Property
     * @var double
     */
    public $appointment_longitude;

    /**
     * @SWG\Property(
     *     type="string",
     *     default="yyyy-MM-dd hh:mm:ss"
     * )
     * @var \DateTime
     */
    public $appointment_time;

    /**
     * @SWG\Property
     * @var string
     */
    public $details;

    /**
     * @SWG\Property
     * @var string
     */
    public $type;

    /**
     * @SWG\Property
     * @var double
     */
    public $approximate_cost;

    /**
     * @SWG\Property(
     *     type="string",
     *     default="yyyy-MM-dd hh:mm:ss"
     * )
     * @var \DateTime
     */
    public $created_at;

    /**
     * @SWG\Property(
     *     type="string",
     *     default="yyyy-MM-dd hh:mm:ss"
     * )
     * @var \DateTime
     */
    public $updated_at;

    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref="#/definitions/EventSetting")
     * )
     * @var array
     */
    public $settings;
}