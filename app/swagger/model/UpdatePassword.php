<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *     type="object",
 *     required={"password"}
 * )
 */
class UpdatePassword
{
    /**
     * @SWG\Property
     * @var string
     */
    public $password;
}