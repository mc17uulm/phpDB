<?php

namespace phpDB\result;

class ResultIntSet extends ResultSet
{

    private int $data;

    public function __construct(bool $success, int $data)
    {
        parent::__construct($success);

        $this->data = $data;
    }

    public function get_value() : int
    {
        return $this->data;
    }

}