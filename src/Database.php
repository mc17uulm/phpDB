<?php

namespace phpDB;

use \PDO;
use \PDOException;

/**
 * Class Connection
 * @package phpDB
 */
class Database
{

    /**
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    /**
     * @param string $host
     * @param int $port
     * @param string $database
     * @param string $user
     * @param string $password
     * @throws DatabaseException
     */
    public static function initialize(string $host, int $port, string $database, string $user, string $password, bool $encrypted) : void
    {
        try {
            self::$connection = new PDO(
                "mysql:host=$host:$port;dbname=$database;charset=utf8",
                $user,
                $password
            );

            if(defined(PHP_DB_DEV) && PHP_DB_DEV) {
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            }
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * @param string $query
     * @param mixed ...$data
     * @return ResultSet
     * @throws DatabaseException
     */
    public static function select(string $query, ...$data) : ResultSet
    {
        return self::execute($query, QueryType::SELECT(), $data);
    }

    /**
     * @param string $query
     * @param mixed ...$data
     * @return ResultSet
     * @throws DatabaseException
     */
    public static function insert(string $query, ...$data) : ResultSet
    {
        return self::execute($query, QueryType::INSERT(), $data);
    }

    /**
     * @param string $query
     * @param mixed ...$data
     * @return ResultSet
     * @throws DatabaseException
     */
    public static function update(string $query, ...$data) : ResultSet
    {
        return self::execute($query, QueryType::UPDATE(), $data);
    }

    /**
     * @param string $query
     * @param mixed ...$data
     * @return ResultSet
     * @throws DatabaseException
     */
    public static function delete(string $query, ...$data) : ResultSet
    {
        return self::execute($query, QueryType::DELETE(), $data);
    }

    /**
     * @param string $query
     * @param QueryType $type
     * @param array $data
     * @return ResultSet
     * @throws DatabaseException
     */
    public static function execute(string $query, QueryType $type, $data) : ResultSet
    {
        $rs = new ResultSet();
        if(!self::is_initialized()) {
            throw new DatabaseException("Connection not initialized");
        }

        try {
            $object = self::$connection->prepare($query);
            $success = $object->execute($data);

            if(!$success) {
                throw new DatabaseException(implode(",", $object->errorInfo()));
            }

            switch($type)
            {
                case QueryType::SELECT():
                    $data = $object->fetchAll(\PDO::FETCH_ASSOC);
                    return $rs->set_success($data);
                case QueryType::UPDATE():
                case QueryType::DELETE():
                    return $rs->set_success();
                case QueryType::INSERT():
                    $id = self::$connection->lastInsertId();
                    return $rs->set_success(["id" => $id]);
                default:
                    throw new DatabaseException("Invalid type");
            }

        } catch(\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * @return bool
     */
    private static function is_initialized() : bool
    {
        return !is_null(self::$connection);
    }

    public static function close() : void
    {
        self::$connection = null;
    }

}