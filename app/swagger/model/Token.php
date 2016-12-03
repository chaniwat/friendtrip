<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"token"}
 * )
 */
class Token
{

    /**
     * @SWG\Property
     * @var string
     */
    public $token;

    /**
     * @SWG\Property(ref="#/definitions/User")
     * @var User
     */
    public $user;

}