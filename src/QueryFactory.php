<?php

namespace phpDB;

class QueryFactory
{

    private QueryType $type;
    private array $selection;
    private string $table;
    private ?string $where_arg = null;
    private ?string $order_arg = null;
    private ?int $limit_arg = null;
    private ?string $column_arg = null;
    private string $value_arg;
    private string $set_arg;
    private array $data;
    private array $validation = [0,0,0,0,0,0,0,0,0,0,0];

    public function select(array $arg = []) : QueryFactory
    {
        $this->set_valid(0);
        $this->selection = $arg;
        return $this->set_type(QueryType::SELECT());
    }

    public function insert() : QueryFactory
    {
        $this->set_valid(1);
        return $this->set_type(QueryType::INSERT());
    }

    public function update(string $table) : QueryFactory
    {
        $this->set_valid(2);
        $this->table = $table;
        return $this->set_type(QueryType::UPDATE());
    }

    public function delete() : QueryFactory
    {
        $this->set_valid(3);
        return $this->set_type(QueryType::DELETE());
    }

    public function from(string $table) : QueryFactory
    {
        $this->set_valid(4);
        return $this->into($table);
    }

    public function into(string $table) : QueryFactory
    {
        $this->set_valid(5);
        $this->table = $table;
        return $this;
    }

    public function where(string $arg, array $data = []) : QueryFactory
    {
        switch($this->type)
        {
            case QueryType::SELECT():
            case QueryType::DELETE():
            case QueryType::UPDATE():
                $this->where_arg = $arg;
                $this->data = array_merge($this->data, $data);
                $this->set_valid(6);
                return $this;
            default:
                throw new QueryException("Invalid method call. 'WHERE' cannot be executed on type '" . $this->type . "'");
        }
    }

    public function order(string $arg) : QueryFactory
    {
        if($this->type !== QueryType::SELECT()) {
            throw new QueryException("Invalid method call. 'ORDER BY' cannot be executed by type'" . $this->type . "'");
        }
        $this->order_arg = $arg;
        $this->set_valid(7);
        return $this;
    }

    public function limit(int $limit) : QueryFactory
    {
        if($this->type !== QueryType::SELECT()) {
            throw new QueryException("Invalid method call. 'ORDER BY' cannot be executed by type'" . $this->type . "'");
        }
        $this->limit_arg = $limit;
        $this->set_valid(8);
        return $this;
    }

    public function values(array $set) : QueryFactory
    {

    }

    private function set_valid(int $index) : void
    {
        if($index > count($this->validation) - 1) {
            throw new QueryException("Invalid use of set_valid method. Index out of bounce.");
        }
        $this->validation[$index] = 1;
    }

    private function set_type(QueryType $type) : QueryFactory
    {
        $this->type = $type;
        return $this;
    }



}