<?php

namespace phpDB;

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
            return new ResultSet(false, "Connection not initialized");
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
                    return new ResultSet(true, $data);
                case QueryType::UPDATE():
                case QueryType::DELETE():
                    return new ResultSet(true);
                case QueryType::INSERT():
                    $id = self::$connection->lastInsertId();
                    return new ResultSet(true, $id);
                default:
                    return new ResultSet(false, "Invalid type");
            }

        } catch(\PDOException $e) {
            return new ResultSet(false, $e->getMessage());
        }
    }

    private static function is_initialized() : bool
    {
        return !is_null(self::$connection);
    }

}