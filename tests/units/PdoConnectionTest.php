<?php

namespace tests\units;

use app\interfaces\IDatabaseConnection;
use app\database\{AbstractConnection, PDOConnection};

class PdoConnectionTest extends \PHPUnit\Framework\TestCase
{
	private static PDOConnection $connection;

	public static function setUpBeforeClass(): void
	{
		static::$connection = new PDOConnection([
			'driver' => 'mysql',
			'db_name' => 'test',
			'host' => 'localhost',
			'username' => 'root',
			'password' => ''
		]);
	}

	public function testItImplementsDatabaseConnectionInterface()
	{
		$this->assertInstanceOf(IDatabaseConnection::class, self::$connection);
	}

	public function testItExtendsAbstractConnection()
	{
		$this->assertTrue(is_subclass_of(PDOConnection::class, AbstractConnection::class));
	}

	public function testItCanConnect()
	{
		$this->assertInstanceOf(\PDO::class, self::$connection->connect()->getConnection());
	}

	public function testItThrowsBadCredentialsExceptionIfCredentialsIsBad()
	{
		$this->expectException(\app\exceptions\BadCredentialsException::class);

		new PDOConnection([]);
	}
}
