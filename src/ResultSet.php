<?php

namespace phpDB;

/**
 * Class ResultSet
 * @package phpDB
 */
class ResultSet
{

    /**
     * @var bool
     */
    private bool $success;
    /**
     * @var mixed
     */
    private $value;

    /**
     * ResultSet constructor.
     * @param bool $success
     * @param mixed $value
     */
    public function __construct(bool $success, $value = null)
    {
        $this->success = $success;
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function was_success() : bool
    {
        return $this->success;
    }

    /**
     * @return mixed
     */
    public function get_value()
    {
        return $this->value;
    }

}