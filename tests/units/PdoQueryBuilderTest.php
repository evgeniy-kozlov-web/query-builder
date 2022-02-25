<?php

namespace tests\units;

use app\database\{QueryBuilder, PDOQueryBuilder, PDOConnection};

class PdoQueryBuilderTest extends \PHPUnit\Framework\TestCase
{
	private PDOQueryBuilder $queryBuilder;

	public function setUp(): void
	{
		$this->queryBuilder = new PDOQueryBuilder(new PDOConnection([
			'driver' => 'mysql',
			'db_name' => 'test',
			'host' => 'localhost',
			'username' => 'root',
			'password' => ''
		]));
	}

	public function tearDown(): void
	{
		$this->queryBuilder->table('foo')->delete()->do();
	}

	public function testItExtendsQueryBuilder()
	{
		$this->assertTrue(is_subclass_of($this->queryBuilder, QueryBuilder::class));
	}

	public function testItCanCreate()
	{
		$id = $this->queryBuilder->table('foo')->create(['name' => 'test'])->lastInsertedId();

		$this->assertEquals(
			[
				'id' => $id,
				'name' => 'test'
			],
			$this->queryBuilder->table('foo')->find($id)->first()
		);
	}

	public function testItCanRead()
	{
		$this->assertEquals(
			[
				[
					'name' => 'test'
				]
			],
			$this->queryBuilder->table('foo')->create(['name' => 'test'])->read(['name'])->do()->get()
		);
	}

	public function testItCanReadFirst()
	{
		$id = $this->queryBuilder->table('foo')->create(['name' => 'test'])->lastInsertedId();

		$this->assertEquals(
			[
				'id' => $id,
				'name' => 'test'
			],
			$this->queryBuilder->table('foo')->read(['*'])->where('id', $id)->do()->first()
		);
	}

	public function testItCanUpdate()
	{
		$id = $this->queryBuilder->table('foo')->create(['name' => 'test'])->lastInsertedId();

		$this->assertEquals(
			[
				'id' => $id,
				'name' => 'notest'
			],
			$this->queryBuilder->update(['name' => 'notest'])->where('id', $id)->do()->read(['*'])->where('id', $id)->do()->first()
		);
	}

	public function testItCanFind()
	{
		$id = $this->queryBuilder->table('foo')->create(['name' => 'test'])->lastInsertedId();

		$this->assertEquals(
			[
				'id' => $id,
				'name' => 'test'
			],
			$this->queryBuilder->table('foo')->find($id)->first()
		);
	}

	public function testItCanFindBy()
	{
		$id = $this->queryBuilder->table('foo')->create(['name' => 'testing'])->lastInsertedId();

		$this->assertEquals(
			[
				'id' => $id,
				'name' => 'testing'
			],
			$this->queryBuilder->findBy('name', 'testing')->first()
		);
	}

	public function testItCanSetWhere()
	{
		$this->assertEmpty($this->queryBuilder->table('foo')->create(['name' => 'test'])->read(['*'])->where('name', 'nottest')->do()->get());
	}

	public function testItCanSetOperationsInWhere()
	{
		$this->assertNotEmpty($this->queryBuilder->table('foo')->create(['name' => 'test'])->read(['*'])->where('name', 'nottest', '<>')->do()->get());
	}

	public function testItCanDelete()
	{
		$id = $this->queryBuilder->table('foo')->create(['name' => 'testing'])->lastInsertedId();

		$this->assertEmpty($this->queryBuilder->table('foo')->delete()->where('id', $id)->do()->find($id)->first());
	}
}
