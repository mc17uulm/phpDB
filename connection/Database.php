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
            Config::get("DATABASE_PORT"),
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

    public static function select(string $query, array $values = array(), bool $return_all = true)
    {

        return self::execute($query, $values, function($q, $e) use ($return_all) {

            if($q->rowCount() > 0)
            {
                return $return_all ? $q->fetchAll(PDO::FETCH_ASSOC) : $q->fetch(PDO::FETCH_ASSOC);
            }
            else
            {
                return array();
            }

        });

    }

    public static function update(string $query, array $values = array(), bool $get_result = false)
    {
        return self::execute($query, $values, function ($q, $e) use ($get_result) {
                return $e;
        });
    }

    public static function insert(string $query, array $values = array(), bool $get_result = false)
    {
        return self::update($query, $values, $get_result);
    }

    public static function delete(string $query, array $values = array(), bool $get_result = false)
    {
        return self::update($query, $values, $get_result);
    }

    public static function execute(string $query, array $values = array(), callable $func = null)
    {

        self::check_db();

        try{

            $q = self::$db->prepare($query);
            $e = $q->execute($values);

            return $func === null ? $e : call_user_func_array($func, array($q, $e));

        }
        catch(PDOException $e)
        {
            throw new DatabaseException("PDOException: " . $e->getMessage());
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