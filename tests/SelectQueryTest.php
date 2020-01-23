<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use phpDB\QueryFactory;

final class SelectQueryTest extends TestCase
{

    public function testValidBasicQuery() : void
    {
        $factory = new QueryFactory();
        $factory->select()->from("users");
        $query = $factory->create();
        $this->assertEquals(
            'SELECT * FROM users',
            $query->get_query()
        );
    }

    public function testValidWhereQuery() : void
    {
        $factory = new QueryFactory();
        $factory->select()->from("users")->where("id = :id", array(":id" => 1));
        $query = $factory->create();
        $this->assertEquals(
            'SELECT * FROM users WHERE id = :id',
            $query->get_query()
        );
        $this->assertEquals([":id" => 1], $query->get_data());
    }

    public function testValidOrderQuery() : void
    {
        $factory = new QueryFactory();
        $factory->select()->from("users")->where("id = :id", array(":id" => 2))->order("id ASC");
        $query = $factory->create();
        $this->assertEquals(
            'SELECT * FROM users WHERE id = :id ORDER BY id ASC',
            $query->get_query()
        );
        $this->assertEquals([":id" => 2], $query->get_data());
    }

    public function testValidLimitQuery() : void
    {
        $factory = new QueryFactory();
        $factory->select()->from("users")->where("id = :id", array(":id" => 2))->order("id ASC")->limit(10);
        $query = $factory->create();
        $this->assertEquals(
            'SELECT * FROM users WHERE id = :id ORDER BY id ASC LIMIT 10',
            $query->get_query()
        );
        $this->assertEquals([":id" => 2], $query->get_data());
    }

}