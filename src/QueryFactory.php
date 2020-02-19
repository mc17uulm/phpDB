<?php

namespace phpDB;

/**
 * Class QueryFactory
 * @package phpDB
 *
 * This class is used to create SQL queries and execute them with the static connection class
 */
class QueryFactory
{

    /**
     * @var QueryType
     */
    private QueryType $type;
    /**
     * @var array
     */
    private array $selection;
    /**
     * @var string
     */
    private string $table;
    /**
     * @var string|null
     */
    private ?string $where_arg = null;
    /**
     * @var string|null
     */
    private ?string $order_arg = null;
    /**
     * @var int|null
     */
    private ?int $limit_arg = null;
    /**
     * @var string|null
     */
    private ?string $column_arg = null;
    /**
     * @var string
     */
    private string $value_arg;
    /**
     * @var string
     */
    private string $set_arg;
    /**
     * @var array
     */
    private array $data = [];
    /**
     * @var array
     */
    private array $validation = [0,0,0,0,0,0,0,0,0,0,0];

    /**
     * @param array | string $arg
     * @return QueryFactory
     * @throws QueryException
     */
    public function select($arg = []) : QueryFactory
    {
        $this->set_valid(0);
        if(!is_array($arg)) {
            if(is_string($arg) && ($arg === "*")) {
                $arg = ["*"];
            } else {
                throw new QueryException("Invalid arguments: " . $arg);
            }
        }

        $this->selection = $arg;
        return $this->set_type(QueryType::SELECT());
    }

    /**
     * @return QueryFactory
     * @throws QueryException
     */
    public function insert() : QueryFactory
    {
        $this->set_valid(1);
        return $this->set_type(QueryType::INSERT());
    }

    /**
     * @param string $table
     * @return QueryFactory
     * @throws QueryException
     */
    public function update(string $table) : QueryFactory
    {
        $this->set_valid(2);
        $this->table = $table;
        return $this->set_type(QueryType::UPDATE());
    }

    /**
     * @return QueryFactory
     * @throws QueryException
     */
    public function delete() : QueryFactory
    {
        $this->set_valid(3);
        return $this->set_type(QueryType::DELETE());
    }

    /**
     * @param string $table
     * @return QueryFactory
     * @throws QueryException
     */
    public function from(string $table) : QueryFactory
    {
        $this->set_valid(4);
        $this->table = $table;
        return $this;
    }

    /**
     * @param string $table
     * @return QueryFactory
     * @throws QueryException
     */
    public function into(string $table) : QueryFactory
    {
        $this->set_valid(5);
        $this->table = $table;
        return $this;
    }

    /**
     * @param string $arg
     * @param array $data
     * @return QueryFactory
     * @throws QueryException
     */
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

    /**
     * @param string $arg
     * @return QueryFactory
     * @throws QueryException
     */
    public function order(string $arg) : QueryFactory
    {
        if(!QueryType::SELECT()->equals($this->type)) {
            throw new QueryException("Invalid method call. 'ORDER BY' cannot be executed by type '" . $this->type . "'");
        }
        $this->order_arg = $arg;
        $this->set_valid(7);
        return $this;
    }

    /**
     * @param int $limit
     * @return QueryFactory
     * @throws QueryException
     */
    public function limit(int $limit) : QueryFactory
    {
        if(!QueryType::SELECT()->equals($this->type)) {
            throw new QueryException("Invalid method call. 'ORDER BY' cannot be executed by type '" . $this->type . "'");
        }
        $this->limit_arg = $limit;
        $this->set_valid(8);
        return $this;
    }

    /**
     * @param array $set
     * @return QueryFactory
     * @throws QueryException
     */
    public function values(array $set) : QueryFactory
    {
        if(!QueryType::INSERT()->equals($this->type)) { throw new QueryException("Invalid method call. 'VALUES' cannot be executed by type '" . $this->type . "'"); }
        if(count($set) <= 0) { throw new QueryException("Invalid values"); }
        $has_column = !is_numeric(array_key_first($set));
        $column = "(";
        $value = "(";
        $data = [];
        foreach($set as $k => $v) {
            $data[":$k"] = $v;
            $value .= ":$k, ";
            if($has_column) {
                $column .= "$k, ";
            }
        };

        $value = substr($value, 0, strlen($value) - 2) . ")";
        if($has_column) {
            $column = substr($column, 0, strlen($column) - 2) . ")";
        } else {
            $column = null;
        }

        $this->data = array_merge($this->data, $data);
        $this->value_arg = $value;
        $this->column_arg = $column;
        $this->set_valid(9);
        return $this;
    }

    /**
     * @param array $set
     * @return QueryFactory
     * @throws QueryException
     */
    public function set(array $set) : QueryFactory
    {
        if(!QueryType::UPDATE()->equals($this->type)) { throw new QueryException("Invalid method call. 'SET' cannot be executed by type '" . $this->type . "'"); }
        if(count($set) <= 0 || is_numeric(array_key_first($set))) { throw new QueryException("Invalid values"); }

        $set_arg = "(";
        $data = [];
        foreach($set as $k => $v)
        {
            $set_arg .= "$k = :$k, ";
            $data[":$k"] = $v;
        }

        $this->set_arg = substr($set_arg, 0, strlen($set_arg) - 2) . ")";
        array_push($this->data, $data);
        $this->set_valid(10);
        return $this;
    }

    /**
     * @return Query
     * @throws QueryException
     */
    public function create() : Query
    {
        if(empty($this->type) || empty($this->table)) { throw new QueryException("QueryFactory not build"); }
        switch($this->type)
        {
            case QueryType::SELECT():
                return $this->create_select();
            case QueryType::INSERT():
                return $this->create_insert();
            case QueryType::UPDATE():
                return $this->create_update();
            case QueryType::DELETE():
                return $this->create_delete();
            default:
                throw new QueryException("QueryFactory not build");
        }
    }

    /**
     * @param Query|null $query
     * @return ResultSet
     * @throws QueryException
     */
    public function execute(Query $query = null) : ResultSet
    {
        $query  = is_null($query) ? $this->create() : $query;
        return Connection::execute($query);
    }

    /**
     * @param int $index
     * @throws QueryException
     */
    private function set_valid(int $index) : void
    {
        if($index > count($this->validation) - 1) {
            throw new QueryException("Invalid use of set_valid method. Index out of bounce.");
        }
        $this->validation[$index] = 1;
    }

    /**
     * @param QueryType $type
     * @return QueryFactory
     */
    private function set_type(QueryType $type) : QueryFactory
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Query
     * @throws QueryException
     */
    private function create_select() : Query
    {
        $valid_arr = [1,0,0,0,1,0,-1,-1,-1,0,0];
        if(!$this->validate($valid_arr)) {
            throw new QueryException("Invalid concatenation of arguments");
        }
        $query = "SELECT";
        if(count($this->selection) <= 0) {
            $query .= " *";
        } else {
            foreach($this->selection as $selection) {
                $query .= " $selection,";
            }
            $query = substr($query, 0, -1);
        }
        $query .= " FROM " . $this->table;
        if(!is_null($this->where_arg)) {
            $query .= " WHERE " . $this->where_arg;
        }
        if(!is_null($this->order_arg)) {
            $query .= " ORDER BY " . $this->order_arg;
        }
        if(!is_null($this->limit_arg)) {
            $query .= " LIMIT " . $this->limit_arg;
        }

        return new Query($query, $this->data, $this->type);
    }

    /**
     * @return Query
     * @throws QueryException
     */
    private function create_insert() : Query
    {
        $valid_arr = [0,1,0,0,0,1,0,0,0,-1,0];
        if(!$this->validate($valid_arr)) {
            throw new QueryException("Invalid concatenation of arguments");
        }
        $query = "INSERT INTO " . $this->table;
        if(!is_null($this->column_arg)) {
            $query .= " " . $this->column_arg;
        }
        $query .= " VALUES " . $this->value_arg;

        return new Query($query, $this->data, $this->type);
    }

    /**
     * @return Query
     * @throws QueryException
     */
    private function create_update() : Query
    {
        $valid_arr = [0,0,1,0,0,0,1,0,0,0,1];
        if(!$this->validate($valid_arr)) {
            throw new QueryException("Invalid concatenation of arguments");
        }
        $query = "UPDATE " . $this->table;
        $query .= " SET " . $this->set_arg;
        $query .= " WHERE " . $this->where_arg;

        return new Query($query, $this->data, $this->type);
    }

    /**
     * @return Query
     * @throws QueryException
     */
    private function create_delete() : Query
    {
        $valid_arr = [0,0,0,1,1,0,1,0,0,0,0];
        if(!$this->validate($valid_arr)) {
            throw new QueryException("Invalid concatenation of arguments");
        }
        $query = "DELETE FROM " . $this->table;
        $query .= " WHERE " . $this->where_arg;

        return new Query($query, $this->data, $this->type);
    }

    /**
     * @param array $bits
     * @return bool
     */
    private function validate(array $bits) : bool
    {
        for($i = 0; $i < count($this->validation); $i++)
        {
            if($this->validation[$i] !== $bits[$i]) {
                if($bits[$i] !== -1) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param string $query
     * @param array $data
     * @param QueryType $type
     * @return ResultSet
     * @throws QueryException
     */
    public function raw_sql_execution(string $query, array $data, QueryType $type) : ResultSet
    {
        $raw_query = new Query($query, $data, $type);
        return $this->execute($raw_query);
    }

    /**
     * @return QueryFactory
     */
    public function reset() : QueryFactory
    {
        return new QueryFactory();
    }

}