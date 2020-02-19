<?php

namespace phpDB;

/**
 * Class Result
 * @package phpDB
 */
class Result
{

    /**
     * @var array
     */
    private array $content;

    /**
     * Result constructor.
     * @param array $content
     */
    public function __construct(array $content)
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function get_content() : array
    {
        return $this->content;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws QueryException
     */
    public function get_value(string $key)
    {
        if(isset($this->content[$key])) {
            return $this->content[$key];
        }
        throw new QueryException("Key not set");
    }

}