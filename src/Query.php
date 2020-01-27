<?php

namespace phpDB;

/**
 * Class Query
 * @package phpDB
 */
class Query
{

    /**
     * @var string
     */
    private string $instruction;
    /**
     * @var array
     */
    private array $data;
    /**
     * @var QueryType
     */
    private QueryType $type;

    /**
     * Query constructor.
     * @param string $instruction
     * @param array $data
     * @param QueryType $type
     */
    public function __construct(string $instruction, array $data, QueryType $type)
    {
        $this->instruction = $instruction;
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function get_query() : string
    {
        return $this->instruction;
    }

    /**
     * @return array
     */
    public function get_data() : array
    {
        return $this->data;
    }

    /**
     * @return QueryType
     */
    public function get_type() : QueryType
    {
        return $this->type;
    }

}