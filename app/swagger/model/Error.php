<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"message"}
 * )
 */
class Error
{
    /**
     * @SWG\Property
     * @var string
     */
    public $message;
}