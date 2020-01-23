<?php

namespace phpDB\result;

class ResultSet
{

    private bool $success;

    public function __construct(bool $success)
    {
        $this->success = $success;
    }

    public function was_success() : bool
    {
        return $this->success;
    }

}