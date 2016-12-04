<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"message"}
 * )
 */
class BroadcastMessage
{
    /**
     * @SWG\Property
     * @var string
     */
    public $message;
}