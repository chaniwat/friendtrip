<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      required={"email", "first_name", "last_name", "display_name", "birthdate", "gender", "religion"}
 * )
 */
class NewUser
{
    /**
     * @SWG\Property
     * @var string
     */
    public $email;

    /**
     * @SWG\Property
     * @var string
     */
    public $first_name;

    /**
     * @SWG\Property
     * @var string
     */
    public $last_name;

    /**
     * @SWG\Property
     * @var string
     */
    public $display_name;

    /**
     * @SWG\Property(
     *     type="string",
     *     format="date"
     * )
     * @var \DateTime
     */
    public $birthdate;

    /**
     * @SWG\Property(
     *     enum={"MALE", "FEMALE"}
     * )
     * @var string
     */
    public $gender;

    /**
     * @SWG\Property
     * @var string
     */
    public $religion;

    /**
     * @SWG\Property(
     *     maxLength=10
     * )
     * @var string
     */
    public $phone;
}