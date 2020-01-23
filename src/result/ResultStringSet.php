<?php

namespace phpDB\result;

class ResultStringSet extends ResultSet
{

    private string $data;

    public function __construct(bool $success, string $data = "")
    {
        parent::__construct($success);

        $this->data = $data;
    }

    public function get_value() : string
    {
        return $this->data;
    }

}