<?php

namespace phpDB;

class Query
{

    private string $instruction;
    private array $data;
    private QueryType $type;

    public function __construct(string $instruction, array $data, QueryType $type)
    {
        $this->instruction = $instruction;
        $this->data = $data;
        $this->type = $type;
    }

    public function get_query() : string
    {
        return $this->instruction;
    }

    public function get_data() : array
    {
        return $this->data;
    }

    public function get_type() : QueryType
    {
        return $this->type;
    }

}