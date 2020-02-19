<?php

namespace phpDB;

use \PDO;
use \PDOException;

/**
 * Class Connection
 * @package phpDB
 */
class Connection
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
    public static function initialize(string $host, int $port, string $database, string $user, string $password) : void
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
     * @param Query $query
     * @return ResultSet
     */
    public static function execute(Query $query) : ResultSet
    {
        $rs = new ResultSet();
        if(!self::is_initialized()) {
            return $rs->set_error("Connection not initialized");
        }

        try {
            $object = self::$connection->prepare($query->get_query());
            $success = $object->execute($query->get_data());

            if(!$success) {
                return $rs->set_error(implode(",", $object->errorInfo()));
            }

            switch($query->get_type())
            {
                case QueryType::SELECT():
                    /**if($object->rowCount() <= 0) {
                        return $rs->set_error("No result");
                    }*/
                    $data = $object->fetchAll(\PDO::FETCH_ASSOC);
                    return $rs->set_success(array_map(fn(array $el) => new Result($el), $data));
                case QueryType::UPDATE():
                case QueryType::DELETE():
                    return $rs->set_success();
                case QueryType::INSERT():
                    $id = self::$connection->lastInsertId();
                    return $rs->set_success(new Result(["id" => $id]));
                default:
                    return $rs->set_error("Invalid type");
            }

        } catch(\PDOException $e) {
            return $rs->set_error($e->getMessage());
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