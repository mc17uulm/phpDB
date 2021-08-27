<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use phpDB\Database;
use phpDB\DatabaseException;

final class TestDatabase extends TestCase
{

    /**
     * @test
     * @throws DatabaseException
     */
    public function testCanInit() : void {

        Database::initialize('localhost', 3306, 'test', 'test', '123');
        $this->assertTrue(Database::is_connected());

    }

    /**
     * @test
     * @throws DatabaseException
     */
    public function testErrorOnInvalidCredentials() : void {
        $this->expectException(DatabaseException::class);
        Database::initialize('localhost', 3306, 'test', 'test', '');
    }

    /**
     * @test
     * @throws DatabaseException
     */
    public function testCanCreateTable() : void {
        Database::update(
            "CREATE TABLE IF NOT EXISTS `test`.`test` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `username` VARCHAR(155) NOT NULL,
                `age` INT NOT NULL,
                `adult` BIT(1) NOT NULL,
                PRIMARY KEY (`id`) 
            ) ENGINE = InnoDB;"
        );
        $this->assertCount(1, Database::select("SHOW TABLES LIKE 'test'"));
    }


    public function testThrowsExceptionOnInvalidTable() : void {
        $this->expectException(DatabaseException::class);
        Database::update("CREATE TABLE IF NOT EXISTS `test`.`bad` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `username` INVALID NOT NULL,
                `age` INT NOT NULL,
                `adult` BIT(1) NOT NULL,
                PRIMARY KEY (`id`) 
            ) ENGINE = InnoDB;");
    }

    /**
     * @test
     * @throws DatabaseException
     */
    public function testCanInsertAndSelect() : void {
        Database::insert(
            "INSERT INTO test (username, age, adult) VALUES (?, ?, ?)",
            'tester',
            21,
            true
        );
        $res = Database::select('SELECT * FROM test');
        $this->assertCount(1, $res);
        $this->assertEquals('tester', $res[0]['username']);
        $this->assertEquals(21, $res[0]['age']);
        $this->assertEquals(true, $res[0]['adult']);
    }

    /**
     * @test
     * @throws DatabaseException
     */
    public function testCanUpdate() : void {
        Database::update(
            "UPDATE test SET username = ?",
            'better_tester'
        );
        $res = Database::select('SELECT * FROM test');
        $this->assertCount(1, $res);
        $this->assertEquals('better_tester', $res[0]['username']);
    }

    /**
     * @test
     * @throws DatabaseException
     */
    public function testCanDelete() : void {
        Database::delete("DELETE FROM test WHERE username = ?", 'better_tester');
        $res = Database::select("SELECT * FROM test");
        $this->assertCount(0, $res);
    }

    /**
     * @test
     * @throws DatabaseException
     */
    public function testCanDeleteTable() : void {
        Database::delete("DROP TABLE test");
        $this->assertCount(0, Database::select("SHOW TABLES LIKE 'test'"));
    }

}