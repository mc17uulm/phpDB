<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 08.03.2019
 * Time: 20:40
 */

namespace PHPDatabase\connection;

use \PDO;
use \PDOException;
use PHPDatabase\config\Config;

class Database
{

    private static $db = -1;

    public static function load_from_env($dev = false) : void
    {
        self::load(
            Config::get("DATABASE_HOST"),
            intval(Config::get("DATABASE_PORT")),
            Config::get("DATABASE_NAME"),
            Config::get("DATABASE_USER"),
            Config::get("DATABASE_PASSWORD"),
            $dev
        );
    }

    public static function load(string $host, int $port, string $db_name, string $user, string $password, bool $dev = false) : void
    {
        try
        {

            self::$db = new PDO("mysql:host=$host:$port;dbname=$db_name;charset=utf8", $user, $password);

            if($dev)
            {
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            }

        }
        catch(PDOException $e)
        {
            throw new DatabaseException("PDOException: " . $e->getMessage());
        }
    }

    public static function is_loaded() : bool
    {
        return self::$db !== -1;
    }

    public static function select(string $query, array $values = array(), int $type = 0)
    {

        return self::execute($query, $values, function($q, $e) use ($type) {

            if($q->rowCount() > 0)
            {
                switch($type)
                {
                    case ReturnType::SET:
                    case ReturnType::SINGLE:
                        return new Result(true, $q->fetch(PDO::FETCH_ASSOC));
                    case ReturnType::ALL:
                        return new Result(true, $q->fetchAll(PDO::FETCH_ASSOC));
                    case ReturnType::ID:
                        return new Result(false, "SELECT query not supporting ID");
                    default:
                        return new Result(false);
                }
            }
            else
            {
                return new Result(true, array());
            }

        }, $type);

    }

    public static function update(string $query, array $values = array(), int $type = 0)
    {
        return self::execute($query, $values, function ($q, $e) use ($type) {

            switch($type)
            {
                case ReturnType::SET:
                case ReturnType::SINGLE:
                case ReturnType::ALL:
                    return new Result(true);
                case ReturnType::ID:
                    return new Result(true, $e);
                default:
                    return new Result(false);
            }
        }, $type);
    }

    public static function insert(string $query, array $values = array(), int $type = 0)
    {
        return self::update($query, $values, $type);
    }

    public static function delete(string $query, array $values = array(), int $type = 0)
    {
        return self::update($query, $values, $type);
    }

    public static function execute(string $query, array $values = array(), callable $func = null, int $type = 0)
    {

        self::check_db();

        try{

            $q = self::$db->prepare($query);
            $e = $q->execute($values);

            if($type === ReturnType::ID) {
                $e = self::$db->lastInsertId();
            }

            return $func === null ? $e : call_user_func_array($func, array($q, $e));

        }
        catch(PDOException $e)
        {
            return new Result(false, $e->getMessage());
        }

    }

    private static function check_db() : void
    {
        if(self::$db === -1)
        {
            throw new DatabaseException("Database not initalized");
        }
    }

}