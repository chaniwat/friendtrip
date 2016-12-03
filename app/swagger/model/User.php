<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      allOf={
 *          @SWG\Items(ref="#/definitions/NewUser")
 *      },
 *      required={"id"}
 * )
 */
class User
{
    /**
     * @SWG\Property
     * @var integer
     */
    public $id;
}