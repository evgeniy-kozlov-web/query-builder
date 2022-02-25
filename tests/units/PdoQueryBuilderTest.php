<?php

namespace tests\units;

use app\database\{QueryBuilder, PDOQueryBuilder, PDOConnection};

class PdoQueryBuilderTest extends \PHPUnit\Framework\TestCase
{
	private static PDOQueryBuilder $queryBuilder;
	private static int $id;

	public static function setUpBeforeClass(): void
	{
		static::$queryBuilder = new PDOQueryBuilder(new PDOConnection([
			'driver' => 'mysql',
			'db_name' => 'test',
			'host' => 'localhost',
			'username' => 'root',
			'password' => ''
		]));

		static::$id = static::$queryBuilder->table('foo')->create(['name' => 'test'])->lastInsertedId();
	}

	public function testItExtendsQueryBuilder()
	{
		$this->assertTrue(is_subclass_of(self::$queryBuilder, QueryBuilder::class));
	}

	public function testItCanCreate()
	{
		$this->assertEquals(
			[
				'id' => self::$id,
				'name' => 'test'
			],
			self::$queryBuilder->table('foo')->find(self::$id)->first()
		);
	}

	public function testItCanRead()
	{
		$this->assertNotEmpty(self::$queryBuilder->table('foo')->read(['name'])->do()->get());
	}

	public function testItCanReadFirst()
	{
		$this->assertEquals(
			[
				'id' => self::$id,
				'name' => 'test'
			],
			self::$queryBuilder->table('foo')->find(self::$id)->first()
		);
	}

	public function testItCanUpdate()
	{
		$this->assertEquals(
			[
				'id' => self::$id,
				'name' => 'notest'
			],
			self::$queryBuilder->table('foo')->update(['name' => 'notest'])->where('id', self::$id)->do()->find(self::$id)->first()
		);
	}

	public function testItCanFind()
	{
		$this->assertEquals(
			[
				'id' => self::$id,
				'name' => 'notest'
			],
			self::$queryBuilder->table('foo')->find(self::$id)->first()
		);
	}

	public function testItCanFindBy()
	{
		$this->assertEquals(
			[
				'id' => self::$id,
				'name' => 'notest'
			],
			self::$queryBuilder->findBy('name', 'notest')->first()
		);
	}

	public function testItCanSetWhere()
	{
		$this->assertEmpty(self::$queryBuilder->table('foo')->where('name', 'testing')->read(['*'])->do()->get());
	}

	public function testItCanDelete()
	{
		$this->assertEmpty(self::$queryBuilder->table('foo')->delete()->where('id', self::$id)->do()->find(self::$id)->first());
	}
}
