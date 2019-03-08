<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 08.03.2019
 * Time: 20:47
 */

namespace PHPDatabase\connection;

class DatabaseException extends \Exception
{

    public function __construct($message, $code = 0) {
        parent::__construct(__CLASS__ . ": " . $message, $code);
    }

};