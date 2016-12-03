<?php

namespace App\Swagger\Model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"email", "password"}
 * )
 */
class AuthenticationInfo {

    /**
     * @SWG\Property
     * @var string
     */
    public $email;

    /**
     * @SWG\Property
     * @var string
     */
    public $password;

    /**
     * @SWG\Property(
     *     default=false
     * )
     * @var boolean
     */
    public $get_info;

}