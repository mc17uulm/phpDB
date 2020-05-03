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
     * @var array
     */
    private array $results;

    /**
     * @var string
     */
    private string $err_msg;

    /**
     * ResultSet constructor.
     */
    public function __construct()
    {
        $this->success = false;
        $this->results = [];
        $this->err_msg = "not initialized";
    }

    /**
     * @param array
     * @return ResultSet
     */
    public function set_success(array $results = []) : ResultSet {
        $this->success = true;
        $this->results = $results;
        $this->err_msg = "";
        return $this;
    }

    public function set_error(string $msg) : ResultSet {
        $this->success = false;
        $this->results = [];
        $this->err_msg = $msg;
        return $this;
    }

    /**
     * @return bool
     */
    public function was_success() : bool
    {
        return $this->success;
    }

    /**
     * @return array
     * @throws QueryException
     */
    public function get_results() : array
    {
        if(!$this->success) throw new QueryException("Execution error: no result");
        return $this->results;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws QueryException
     */
    public function get_result(int $id)
    {
        if(!$this->success) throw new QueryException("Execution error: no result");
        if(($id >= 0) && isset($this->results[$id])) {
            return $this->results[$id];
        }
        throw new QueryException("Invalid key: " . $id);
    }

    /**
     * @return array
     * @throws QueryException
     */
    public function get_first_result() : array
    {
        return $this->get_result(0);
    }

    /**
     * @return string
     */
    public function get_error_msg() : string
    {
        return $this->success ? "no error" : $this->err_msg;
    }

    /**
     * @return int
     */
    public function get_size() : int
    {
        return count($this->results);
    }

}