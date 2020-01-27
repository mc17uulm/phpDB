<?php

namespace phpDB;

class ResultSet
{

    private bool $success;
    private $value;

    public function __construct(bool $success, $value = null)
    {
        $this->success = $success;
        $this->value = $value;
    }

    public function was_success() : bool
    {
        return $this->success;
    }

    public function get_value()
    {
        return $this->value;
    }

}