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
     * @var Collection
     */
    private Collection $results;

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
        $this->results = new Collection();
        $this->err_msg = "not initialized";
    }

    /**
     * @param array
     * @return ResultSet
     */
    public function set_success(array $results = []) : ResultSet {
        $this->success = true;
        $this->results = (new Collection($results))->map(fn($el) => is_array($el) ? new Collection($el) : $el);
        $this->err_msg = "";
        return $this;
    }

    public function set_error(string $msg) : ResultSet {
        $this->success = false;
        $this->results = new Collection();
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
     * @return Collection
     * @throws QueryException
     */
    public function get_results() : Collection
    {
        if(!$this->success) throw new QueryException("Execution error: no result");
        return $this->results;
    }

    /**
     * @param int $id
     * @return Collection | mixed
     * @throws QueryException
     * @throws CollectionException
     */
    public function get_result(int $id)
    {
        if(!$this->success) throw new QueryException("Execution error: no result");
        return $this->results->get($id);
    }

    /**
     * @return Collection
     * @throws CollectionException
     */
    public function get_first_result() : Collection
    {
        return $this->results->get(0);
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
        return $this->results->length();
    }

}