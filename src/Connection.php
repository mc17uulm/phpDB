<?php

namespace phpDB;

use phpDB\result\ResultArraySet;
use phpDB\result\ResultIntSet;
use phpDB\result\ResultSet;
use phpDB\result\ResultStringSet;

class Connection
{

    private static ?\PDO $connection = null;

    public static function initialize(string $host, int $port, string $database, string $user, string $password) : void
    {
        try {
            self::$connection = new \PDO(
                "mysql:host=$host:$port;dbname=$database;charset=utf8",
                $user,
                $password
            );

            if(defined(PHP_DB_DEV) && PHP_DB_DEV) {
                self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function execute(Query $query) : ResultSet
    {
        if(!self::is_initialized()) {
            return new ResultStringSet(false, "Connection not initialized");
        }

        try {
            $object = self::$connection->prepare($query->get_query());
            $object->execute($query->get_data());

            switch($query->get_type())
            {
                case QueryType::SELECT():
                    if($object->rowCount() <= 0) {
                        return new ResultSet(false);
                    }
                    $data = $object->fetchAll(\PDO::FETCH_ASSOC);
                    return new ResultArraySet(true, $data);
                case QueryType::UPDATE():
                case QueryType::DELETE():
                    return new ResultSet(true);
                case QueryType::INSERT():
                    $id = self::$connection->lastInsertId();
                    return new ResultIntSet(true, $id);
                default:
                    return new ResultStringSet(false, "Invalid type");
            }

        } catch(\PDOException $e) {
            return new ResultStringSet(false, $e->getMessage());
        }
    }

    private static function is_initialized() : bool
    {
        return !is_null(self::$connection);
    }

}