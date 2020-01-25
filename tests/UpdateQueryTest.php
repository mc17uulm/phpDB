<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use phpDB\QueryFactory;

class UpdateQueryTest extends TestCase
{

    public function testValidBasicQuery() : void
    {
        $factory = new QueryFactory();
        $factory->update("users")->set(["name" => "new name", "mail" => "new mail"])->where("id = :id", [":id" => 1]);
        $query = $factory->create();
        $this->assertEquals(
            "UPDATE users SET (name = :name, mail = :mail) WHERE id = :id",
            $query->get_query()
        );
    }

}