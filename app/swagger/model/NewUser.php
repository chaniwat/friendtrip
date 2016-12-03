<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"user", "password"}
 * )
 */
class NewUser
{

    /**
     * @SWG\Property(ref="#/definitions/UserBody")
     * @var UserBody
     */
    public $user;

    /**
     * @SWG\Property
     * @var string
     */
    public $password;

}