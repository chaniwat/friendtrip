<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *     type="object",
 *     required={"event_setting_id", "value"}
 * )
 */
class EventSetting
{
    /**
     * @SWG\Property
     * @var string
     */
    public $event_setting_id;

    /**
     * @SWG\Property
     * @var string
     */
    public $value;
}