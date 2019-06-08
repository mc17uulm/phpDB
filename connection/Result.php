<?php


namespace PHPDatabase\connection;

class Result
{

    private $status;
    private $data;

    public function __construct(bool $status, $data = "")
    {
        $this->status = $status;
        $this->data = $data;
    }

    public function isValid() : bool
    {
        return $this->status;
    }

    public function getObject()
    {
        return $this->data;
    }

}