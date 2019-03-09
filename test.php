<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 09.03.2019
 * Time: 14:21
 */

require_once "vendor/autoload.php";

\PHPDatabase\config\Config::load();
try {
    \PHPDatabase\connection\Database::load_from_env(true);
} catch (\PHPDatabase\connection\DatabaseException $e)
{
    echo "Exc:\r\n";
    var_dump($e->getMessage());
    die();
}