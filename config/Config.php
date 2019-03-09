<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 08.03.2019
 * Time: 21:30
 */

namespace PHPDatabase\config;

use Dotenv\Dotenv;

class Config
{

    public static function load(string $dir = __DIR__)
    {

        $dotenv = Dotenv::create($dir);
        $dotenv->load();

    }

    public static function get(string $key) : string
    {
        return $_ENV[$key] ?? "";
    }

}