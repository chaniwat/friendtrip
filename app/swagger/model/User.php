<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *      type="object",
 *      allOf={
 *          @SWG\Items(ref="#/definitions/UserBody")
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