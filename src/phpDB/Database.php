<?php

namespace phpDB;

use PDO;
use PDOException;

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
    public static function initialize(string $host, int $port, string $database, string $user, string $password) : void
    {
        self::connect($host, $port, $database, $user, $password);
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $database
     * @param string $user
     * @param string $password
     * @param bool $debug
     * @throws DatabaseException
     */
    public static function connect(string $host, int $port, string $database, string $user, string $password, bool $debug = false) : void
    {
        try {
            self::$connection = new PDO(
                "mysql:host=$host:$port;dbname=$database;charset=utf8",
                $user,
                $password
            );

            if($debug) {
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function close() : void {
        self::$connection = null;
    }

    /**
     * @return bool
     */
    public static function is_connected() : bool {
        return !is_null(self::$connection);
    }

    /**
     * @param string $query
     * @param mixed ...$data
     * @return array
     * @throws DatabaseException
     */
    public static function select(string $query, ...$data) : array {
        return self::execute($query, "SELECT", $data);
    }

    /**
     * @param string $query
     * @param mixed ...$data
     * @return int
     * @throws DatabaseException
     */
    public static function insert(string $query, ...$data) : int {
        return self::execute($query, "INSERT", $data);
    }

    /**
     * @param string $query
     * @param mixed ...$data
     * @return bool
     * @throws DatabaseException
     */
    public static function update(string $query, ...$data) : bool {
        return self::execute($query, "UPDATE", $data);
    }

    /**
     * @param string $query
     * @param mixed ...$data
     * @return bool
     * @throws DatabaseException
     */
    public static function delete(string $query, ...$data) : bool {
        return self::execute($query, "DELETE", $data);
    }

    /**
     * @param mixed $var
     * @return int
     * @throws DatabaseException
     */
    private static function get_type(mixed $var) : int {
        $type = gettype($var);
        return match ($type) {
            'integer', 'double' => PDO::PARAM_INT,
            'string' => PDO::PARAM_STR,
            'boolean' => PDO::PARAM_BOOL,
            'NULL' => PDO::PARAM_NULL,
            default => throw new DatabaseException("Invalid type '$type"),
        };
    }

    /**
     * @param string $query
     * @param string $type
     * @param array $data
     * @return array|int|bool
     * @throws DatabaseException
     */
    private static function execute(string $query, string $type, array $data): int|bool|array
    {

        if(!self::is_connected()) {
            throw new DatabaseException("Connection not initialized");
        }

        try {
            $statement = self::$connection->prepare($query);

            array_walk($data, function(mixed $var, int $i) use($statement) {
                $statement->bindValue($i + 1, $var, self::get_type($var));
            });

            if(!$statement->execute()) {
                throw new DatabaseException(implode(";", $statement->errorInfo()));
            }

            return match ($type) {
                "SELECT" => $statement->fetchAll(PDO::FETCH_ASSOC),
                "UPDATE", "DELETE" => true,
                "INSERT" => intval(self::$connection->lastInsertId()),
                default => throw new DatabaseException("Invalid query type '$type'"),
            };

        } catch(PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

}