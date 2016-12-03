<?php

namespace app\swagger\model;

/**
 * @SWG\Definition(
 *     type="object",
 *     required={"total", "per_page", "current_page", "last_page", "next_page_url", "last_page_url", "from", "to"}
 * )
 */
class Pagination
{
    /**
     * @SWG\Property
     * @var integer
     */
    public $total;

    /**
     * @SWG\Property
     * @var integer
     */
    public $per_page;

    /**
     * @SWG\Property
     * @var integer
     */
    public $current_page;

    /**
     * @SWG\Property
     * @var integer
     */
    public $last_page;

    /**
     * @SWG\Property(
     *     default="string or null"
     * )
     * @var string
     */
    public $next_page_url;

    /**
     * @SWG\Property(
     *     default="string or null"
     * )
     * @var string
     */
    public $prev_page_url;

    /**
     * @SWG\Property
     * @var integer
     */
    public $from;

    /**
     * @SWG\Property
     * @var integer
     */
    public $to;
}