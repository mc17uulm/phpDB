<?php

namespace phpDB\result;

class ResultArraySet extends ResultSet
{

    public array $data;

    public function __construct(bool $success, array $data = [])
    {
        parent::__construct($success);

        $this->data = $data;
    }

    public function get_value() : array
    {
        return $this->data;
    }

}